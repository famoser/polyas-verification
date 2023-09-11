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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

class SlimExtensions
{
    public const STATUS_OK = 200;

    /**
     * @return string[]
     */
    public static function parseJsonRequestBody(Request $request): array
    {
        $bodyContents = $request->getBody()->getContents();

        return json_decode($bodyContents, true);
    }

    /**
     * @param string[]|null $receipt
     */
    public static function createStatusJsonResponse(Request $request, Response $response, bool $status, string $error = null, string $result = null, array $receipt = null): Response
    {
        $jsonContent = json_encode([
            'status' => $status,
            'error' => $error,
            'result' => $result,
            'receipt' => $receipt,
        ]);

        return self::createJsonResponse($request, $response, $jsonContent);
    }

    public static function createPdfFileResponse(Response $response, bool $status, string $filename, string $file = null): Response
    {
        if ($file) {
            $response->getBody()->write($file);
        }

        return $response
            ->withStatus($status ? 200 : 500)
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public static function createJsonResponse(Request $request, Response $response, mixed $body, int $statusCode = self::STATUS_OK): Response
    {
        $jsonContent = json_encode($body);
        if (!$jsonContent) {
            throw new HttpInternalServerErrorException($request, 'cannot serialize to json');
        }

        $response->getBody()->write($jsonContent);

        return $response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}
