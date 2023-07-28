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

readonly class ChallengeCommit
{
    public function __construct(private \GMP $e, private \GMP $r)
    {
    }

    public function commit(): string
    {
        $generatorG = EccFactory::getSecgCurves()->generator256k1();
        $generatorH = SECP256K1\Encoder::parseCompressedPoint(GlobalParameters::getPOLYASCommitmentGeneratorH());

        $commitment = new PedersonCommit($generatorG, $generatorH);
        $point = $commitment->commit($this->e, $this->r);

        return SECP256K1\Encoder::compressPoint($point);
    }
}
