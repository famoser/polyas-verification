<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\POLYAS\Utils;

use Famoser\PolyasVerification\Crypto\BCMATH\Hex;

class Serialization
{
    public static function bcdechexFixed(string $dec): string
    {
        $hex = Hex::bcdechex($dec);

        if (1 === strlen($hex) % 2) {
            $hex = '0'.$hex;
        }

        // TODO verify this is actually what happens
        if ($hex[0] > '7') {
            $hex = '00'.$hex;
        }

        return $hex;
    }

    public static function getStringHexWithLength(string $content): string
    {
        $content = bin2hex($content);

        return self::getHexLength4Bytes((int) (strlen($content) / 2)).$content;
    }

    /**
     * @param mixed[] $collection
     */
    public static function getCollectionHexLength4Bytes(array $collection): string
    {
        return self::getHexLength4Bytes(count($collection));
    }

    public static function getBytesHexLength4Bytes(string $content): string
    {
        return self::getHexLength4Bytes((int) (strlen($content) / 2));
    }

    public static function getHexLength4Bytes(int $length): string
    {
        $hexLength = dechex($length);

        return str_pad($hexLength, 8, '0', STR_PAD_LEFT);
    }
}
