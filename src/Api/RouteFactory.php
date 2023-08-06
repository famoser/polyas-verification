<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Api;

use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use Famoser\PolyasVerification\PathHelper;
use Famoser\PolyasVerification\Storage;
use Famoser\PolyasVerification\Workflow\ApiClient;
use Famoser\PolyasVerification\Workflow\Receipt;
use Famoser\PolyasVerification\Workflow\Verification;
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
            $electionJsonFile = PathHelper::ELECTION_JSON_FILE;
            $election = Storage::readJsonFile($electionJsonFile);

            return SlimExtensions::createJsonResponse($request, $response, $election);
        });

        $route->get('/electionDetails', function (Request $request, Response $response, array $args) {
            $apiClient = self::createPOLYASApiClient();
            $election = $apiClient->getElection();

            return SlimExtensions::createJsonResponse($request, $response, $election);
        });

        $route->post('/receipt', function (Request $request, Response $response, array $args) {
            /** @var UploadedFile|false $file */
            $file = current($request->getUploadedFiles());
            if (!$file) {
                throw new HttpBadRequestException($request, 'No file uploaded');
            }
            RequestValidatorExtensions::checkPdfFileUploadSuccessful($request, $file);
            $path = Storage::writeUploadedFile(PathHelper::VAR_TRANSIENT_DIR, $file);

            $deviceParametersPath = PathHelper::DEVICE_PARAMETERS_JSON_FILE;
            $deviceParameters = Storage::readJsonFile($deviceParametersPath);

            $receipt = new Receipt($deviceParameters['verificationKey']);
            $result = $receipt->verify($path, $failedCheck);
            Storage::removeFile($path);

            return SlimExtensions::createStatusJsonResponse($request, $response, $result, $failedCheck);
        });

        $route->post('/verification', function (Request $request, Response $response, array $args) {
            $payload = SlimExtensions::parseJsonRequestBody($request);
            RequestValidatorExtensions::checkVerification($request, $payload);
            /** @var array{
             *     'payload': string,
             *     'voterId': string,
             *     'nonce': string,
             *     'password': string,
             * } $payload
             */
            $deviceParametersPath = PathHelper::DEVICE_PARAMETERS_JSON_FILE;
            $deviceParametersJson = Storage::readFile($deviceParametersPath);

            $apiClient = self::createPOLYASApiClient();
            $verification = new Verification($deviceParametersJson, $apiClient);
            $challengeCommit = ChallengeCommit::createWithRandom();
            $result = $verification->verify($payload, $challengeCommit, $failedCheck);

            return SlimExtensions::createStatusJsonResponse($request, $response, null !== $result, $failedCheck, $result);
        });
    }

    public static function createPOLYASApiClient(): ApiClient
    {
        $path = PathHelper::ELECTION_JSON_FILE;
        $content = Storage::readJsonFile($path);

        return new ApiClient($content['polyasElection']);
    }
}
