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
use Mdanter\Ecc\Primitives\PointInterface;

class Math
{
    public static function inverse(PointInterface $point): PointInterface
    {
        $curve = EccFactory::getSecgCurves()->curve256k1();
        $invertedY = EccFactory::getAdapter()->sub($curve->getPrime(), $point->getY());

        return $curve->getPoint($point->getX(), $invertedY);
    }
}
