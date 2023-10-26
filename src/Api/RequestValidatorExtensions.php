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

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\UploadedFile;

class RequestValidatorExtensions
{
    public static function checkPdfFileUploadSuccessful(Request $request, ?UploadedFile $file): void
    {
        if (!$file) {
            throw new HttpBadRequestException($request, 'file upload failed; no file was received by the server.');
        }

        if (UPLOAD_ERR_OK !== $file->getError()) {
            throw new HttpBadRequestException($request, 'file upload failed with code '.$file->getError().'.');
        }

        if ($file->getClientFilename() && !str_ends_with($file->getClientFilename(), 'pdf')) {
            throw new HttpBadRequestException($request, 'file upload failed; please upload a pdf file.');
        }
    }

    /**
     * @param string[] $verification
     */
    public static function checkVerification(Request $request, array $verification): void
    {
        RequestValidatorExtensions::checkExactlyKeysSet($request, $verification, ['payload', 'voterId', 'nonce', 'password']);
    }

    /**
     * @param string[] $receipt
     */
    public static function checkReceipt(Request $request, array $receipt): void
    {
        RequestValidatorExtensions::checkExactlyKeysSet($request, $receipt, ['fingerprint', 'signature', 'ballotVoterId']);
    }

    /**
     * @param string[] $array
     * @param string[] $requiredKeys
     */
    public static function checkExactlyKeysSet(Request $request, array $array, array $requiredKeys): void
    {
        foreach ($requiredKeys as $key) {
            if (!key_exists($key, $array)) {
                throw new HttpBadRequestException($request, 'key '.$key.' expected, but not provided.');
            }
        }

        foreach (array_keys($array) as $key) {
            if (!in_array($key, $requiredKeys, true)) {
                throw new HttpBadRequestException($request, 'the key '.$key.' is invalid.');
            }
        }
    }
}
