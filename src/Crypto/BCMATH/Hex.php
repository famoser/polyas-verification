<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\BCMATH;

class Hex
{
    public static function bcdechex(string $dec): string
    {
        $hex = '';
        do {
            $last = (int) bcmod($dec, '16');
            $hex = dechex($last).$hex;
            $dec = bcdiv(bcsub($dec, (string) $last), '16');
        } while ($dec > 0);

        return $hex;
    }

    public static function bchexdec(string $hex): string
    {
        $number = '0';
        $sixteen = '16';

        while (strlen($hex) > 0) {
            $number = bcmul($sixteen, $number);
            $first = substr($hex, 0, 1);
            $number = bcadd($number, (string) hexdec($first));
            $hex = substr($hex, 1);
        }

        return $number;
    }
}
