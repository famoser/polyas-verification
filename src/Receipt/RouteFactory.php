<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Receipt;

use Famoser\PolyasVerification\Crypto\POLYAS\Receipt;
use Famoser\PolyasVerification\PathHelper;
use Famoser\PolyasVerification\Storage;
use Famoser\PolyasVerification\Utils\RequestValidatorExtensions;
use Famoser\PolyasVerification\Utils\SlimExtensions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\UploadedFile;
use Slim\Routing\RouteCollectorProxy;

class RouteFactory
{
    public static function addRoutes(RouteCollectorProxy $route): void
    {
        $route->get('/election', function (Request $request, Response $response, array $args) {
            $path = PathHelper::ELECTION_JSON_FILE;
            $content = Storage::readJsonFile($path);

            return SlimExtensions::createJsonResponse($request, $response, $content);
        });

        $route->post('/receipt', function (Request $request, Response $response, array $args) {
            /** @var UploadedFile|false $file */
            $file = current($request->getUploadedFiles());
            if (!$file) {
                throw new HttpBadRequestException($request, 'No file uploaded');
            }
            RequestValidatorExtensions::checkPdfFileUploadSuccessful($request, $file);
            $path = Storage::writeUploadedFile(PathHelper::VAR_TRANSIENT_DIR, $file);

            $verificationResult = Receipt::verify($path);
            Storage::removeFile($path);

            return SlimExtensions::createJsonResponse($request, $response, $verificationResult);
        });
    }
}
