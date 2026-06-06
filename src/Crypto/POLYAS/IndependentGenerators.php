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

use Famoser\PolyasVerification\Crypto\SECP256K1;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\PointInterface;

readonly class IndependentGenerators
{
    public function __construct(private string $seed)
    {
    }

    public function derive(int $index): PointInterface
    {
        $curve = EccFactory::getSecgCurves()->curve256k1();
        $generator = EccFactory::getSecgCurves()->generator256k1();

        $seed = $this->seed . "ggen" . pack('N', $index);
        $numbersFromSeedInRange = new NumbersFromSeedInRange($seed, gmp_mul(2, $curve->getPrime()));
        foreach ($numbersFromSeedInRange->getIterator() as $randomNumber) {
            $x = gmp_mod($randomNumber, $curve->getPrime());
            if (!SECP256K1\Math::tryGetSquareRoot($x, $root)) {
                continue;
            }

            if (gmp_cmp($randomNumber, $curve->getPrime()) < 0) {
                $root = gmp_mod(gmp_neg($root), $curve->getPrime());
            }

            $point = $curve->getPoint($x, $root, $generator->getOrder());
            if ($point->isInfinity()) {
                continue;
            }

            return $point;
        }

        throw new \RuntimeException("Unreachable code.");
    }
}
