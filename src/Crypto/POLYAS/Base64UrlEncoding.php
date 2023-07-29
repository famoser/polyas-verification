<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\POLYAS;

class Base64UrlEncoding
{
    private const SEARCH = ['/', '+'];
    private const REPLACE = ['_', '-'];

    /**
     * likely motivated by https://base64.guru/standards/base64url.
     */
    public static function encode(string $base64): string
    {
        $safeCharacters = str_replace(self::SEARCH, self::REPLACE, $base64);

        return rtrim($safeCharacters, '=');
    }

    public static function decode(string $base64): string
    {
        $correctCharacters = str_replace(self::REPLACE, self::SEARCH, $base64);
        $padding = str_repeat('=', strlen($correctCharacters) % 4);

        return $correctCharacters.$padding;
    }
}
