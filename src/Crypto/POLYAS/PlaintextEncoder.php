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

namespace Famoser\PolyasVerification\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\POLYAS\Utils\PlaintextEncodingException;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Primitives\PointInterface;

class PlaintextEncoder
{
    private const MAX_COUNTER = 80;

    public static function encode(\GMP $value): PointInterface
    {
        $curve = EccFactory::getSecgCurves()->curve256k1();
        $order = EccFactory::getSecgCurves()->generator256k1()->getOrder();

        $currentValue = $value;
        $counter = 1;

        do {
            try {
                $xPoint = gmp_mod(gmp_add($currentValue, 1), $order);
                $yPoint = $curve->recoverYfromX(false, $xPoint);

                return $curve->getPoint($xPoint, $yPoint);
            } catch (PointRecoveryException) {
                // ok. no need to handle
            }

            $currentValue = gmp_add($currentValue, $value);
        } while ($counter++ < self::MAX_COUNTER); // loop iterates INCLUDING max counter

        throw new PlaintextEncodingException('Value cannot be encoded');
    }

    public static function decode(PointInterface $point): \GMP
    {
        return gmp_div(gmp_sub($point->getX(), 1), self::MAX_COUNTER);
    }
}
