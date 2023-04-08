<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification;

use Slim\Psr7\UploadedFile;

class Storage
{
    public static function writeUploadedFile(string $dir, UploadedFile $file): string
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = uniqid().'.pdf';
        $file->moveTo($dir.'/'.$filename);

        return $filename;
    }
}
