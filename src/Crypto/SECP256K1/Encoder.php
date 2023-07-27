<?php

declare(strict_types=1);

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\SECP256K1;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\Primitives\PointInterface;

class Encoder
{
    /**
     * implements likely compressed format specified by ANSI X9.62 4.3.6
     * but behind paywall; so implementation details from stackexchange
     * https://security.stackexchange.com/a/185552.
     */
    public static function parseCompressedPoint(string $point): PointInterface
    {
        $prefix = substr($point, 0, 2);
        $xHex = substr($point, 2);

        $wasOdd = '03' === $prefix;
        $x = gmp_init('0x'.$xHex, 16);

        $curve = EccFactory::getSecgCurves()->curve256k1();
        $y = $curve->recoverYfromX($wasOdd, $x);

        return $curve->getPoint($x, $y);
    }

    /**
     * reverts @see parseCompressedPoint.
     */
    public static function compressPoint(PointInterface $point): string
    {
        $x = $point->getX();
        $xString = gmp_strval($x, 16);
        $xStringPadded = str_pad($xString, 64, '0', STR_PAD_LEFT);

        $math = MathAdapterFactory::getAdapter();

        $odd = $math->equals($math->mod($point->getY(), gmp_init(2, 10)), gmp_init(1));
        $suffix = $odd ? '03' : '02';

        return $suffix.$xStringPadded;
    }
}
