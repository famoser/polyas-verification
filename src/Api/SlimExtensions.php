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
