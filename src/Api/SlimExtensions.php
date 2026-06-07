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

use Famoser\PolyasVerification\PathHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;
use ZipArchive;

class SlimExtensions
{
    public const int STATUS_OK = 200;

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
    public static function createStatusJsonResponse(Request $request, Response $response, bool $status, ?string $error = null, ?string $result = null, ?array $receipt = null): Response
    {
        $jsonContent = json_encode([
            'status' => $status,
            'error' => $error,
            'result' => $result,
            'receipt' => $receipt,
        ]);

        return self::createJsonResponse($request, $response, $jsonContent);
    }

    public static function createPdfFileResponse(Response $response, bool $status, string $filename, ?string $file = null): Response
    {
        if ($file) {
            $response->getBody()->write($file);
        }

        return $response
            ->withStatus($status ? 200 : 500)
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * @param array<string, string> $files
     */
    public static function createZipFileResponse(Request $request, Response $response, array $files, string $filename): Response
    {
        $path = PathHelper::VAR_TRANSIENT_DIR . "/" . $filename;

        $zip = new ZipArchive();
        if (true !== $zip->open($path, ZipArchive::CREATE |  ZipArchive::OVERWRITE)) {
            unlink($path);

            return self::createStatusJsonResponse($request, $response, false, 'Cannot create zip file');
        }

        foreach ($files as $fileName => $fileContent) {
            $zip->addFromString($fileName, $fileContent);
        }

        $zip->close();
        $zipContent = file_get_contents($path);
        if ($zipContent === false) {
            return self::createStatusJsonResponse($request, $response, false, 'Cannot read zip file ' . $path);
        }

        $response->getBody()->write($zipContent);

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/zip')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
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
