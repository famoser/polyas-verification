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
        $path = $dir.DIRECTORY_SEPARATOR.$filename;

        $file->moveTo($path);

        return $path;
    }

    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public static function readJsonFile(string $path): array
    {
        $content = file_get_contents($path);
        if (!$content) {
            throw new \Exception('File not found');
        }

        return json_decode($content, true);
    }

    public static function removeFile(string $path): void
    {
        unlink($path);
    }
}
