<?php

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
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Exception\SquareRootException;
use Mdanter\Ecc\Math\ModularArithmetic;
use Mdanter\Ecc\Primitives\PointInterface;

class Math
{
    public static function inverse(PointInterface $point): PointInterface
    {
        $curve = EccFactory::getSecgCurves()->curve256k1();
        $invertedY = EccFactory::getAdapter()->sub($curve->getPrime(), $point->getY());

        return $curve->getPoint($point->getX(), $invertedY);
    }

    /**
     * @phpstan-assert-if-true \GMP $root
     */
    public static function tryGetSquareRoot(\GMP $x, ?\GMP &$root = null): bool
    {
        $curve = EccFactory::getSecgCurves()->curve256k1();

        $adapter = EccFactory::getAdapter();
        $modAdapter = new ModularArithmetic($adapter, $curve->getPrime());
        /** @var \GMP $three */
        $three = gmp_init(3, 10);
        try {
            $root = $adapter->getNumberTheory()->squareRootModP(
                $adapter->add(
                    $adapter->add(
                        $modAdapter->pow($x, $three),
                        $adapter->mul($curve->getA(), $x)
                    ),
                    $curve->getB()
                ),
                $curve->getPrime()
            );

            return true;
        } catch (SquareRootException) {
            return false;
        }
    }
}
