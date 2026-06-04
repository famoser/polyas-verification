<?php

namespace Famoser\PolyasVerification\Crypto\POLYAS\Utils;

use Famoser\PolyasVerification\Crypto\PEDERSEN\PedersenCommit;
use Famoser\PolyasVerification\Crypto\POLYAS\GlobalParameters;
use Mdanter\Ecc\EccFactory;
use Famoser\PolyasVerification\Crypto\SECP256K1;

class PedersenFactory
{
    public static function createPedersen(): PedersenCommit
    {
        $generatorG = EccFactory::getSecgCurves()->generator256k1();
        $generatorH = SECP256K1\Encoder::parseCompressedPoint(GlobalParameters::getPOLYASCommitmentGeneratorH());

        return new PedersenCommit($generatorG, $generatorH);
    }
}
