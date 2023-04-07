<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Utils;

use Psr\Http\Message\ResponseInterface as Response;

class SlimExtensions
{
    public const STATUS_OK = 200;

    public static function createJsonResponse(Response $response, array $body, int $statusCode = self::STATUS_OK): Response
    {
        $jsonContent = json_encode($body);
        $response->getBody()->write($jsonContent);

        return $response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}
