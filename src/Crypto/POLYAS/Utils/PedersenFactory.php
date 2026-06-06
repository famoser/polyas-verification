<?php

namespace Famoser\PolyasVerification\Crypto\POLYAS\Utils;

use Famoser\PolyasVerification\Crypto\PEDERSEN\PedersenCommit;
use Famoser\PolyasVerification\Crypto\POLYAS\IndependentGenerators;
use Mdanter\Ecc\EccFactory;

class PedersenFactory
{
    public static function createPedersen(): PedersenCommit
    {
        $independentGenerator = new IndependentGenerators("pedersen-commitment-key");
        $generatorG = EccFactory::getSecgCurves()->generator256k1();
        $generatorH = $independentGenerator->derive(10);

        return new PedersenCommit($generatorG, $generatorH);
    }
}
