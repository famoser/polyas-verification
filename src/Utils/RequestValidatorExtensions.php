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

        if (str_ends_with($file->getClientFilename(), 'pdf')) {
            throw new HttpBadRequestException($request, 'file upload failed; please upload a pdf file.');
        }
    }
}
