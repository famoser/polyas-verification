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

use Famoser\PolyasVerification\Crypto\PEDERSON\PedersonCommit;
use Famoser\PolyasVerification\Crypto\SECP256K1;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\PointInterface;
use Mdanter\Ecc\Random\RandomGeneratorFactory;

readonly class ChallengeCommit
{
    public function __construct(private \GMP $e, private \GMP $r)
    {
    }

    public static function createWithRandom(): self
    {
        $generatorG = EccFactory::getSecgCurves()->generator256k1();
        $random = RandomGeneratorFactory::getRandomGenerator();
        $randomE = $random->generate($generatorG->getOrder());
        $randomR = $random->generate($generatorG->getOrder());

        return new self($randomE, $randomR);
    }

    public function commit(): string
    {
        $point = $this->commitPoint();

        return SECP256K1\Encoder::compressPoint($point);
    }

    public function verify(string $commit): bool
    {
        $commitPoint = SECP256K1\Encoder::parseCompressedPoint($commit);

        return $this->commitPoint()->equals($commitPoint);
    }

    private function commitPoint(): PointInterface
    {
        $generatorG = EccFactory::getSecgCurves()->generator256k1();
        $generatorH = SECP256K1\Encoder::parseCompressedPoint(GlobalParameters::getPOLYASCommitmentGeneratorH());

        $commitment = new PedersonCommit($generatorG, $generatorH);

        return $commitment->commit($this->e, $this->r);
    }

    public function getE(): \GMP
    {
        return $this->e;
    }

    public function getEString(): string
    {
        return gmp_strval($this->e);
    }

    public function getRString(): string
    {
        return gmp_strval($this->r);
    }
}
