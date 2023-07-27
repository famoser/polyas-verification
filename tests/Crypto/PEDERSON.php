<?php

namespace Famoser\PolyasVerification\Test\Crypto;

use Famoser\PolyasVerification\Crypto\PEDERSON\PedersonCommit;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use PHPUnit\Framework\TestCase;

class PEDERSON extends TestCase
{
    public function testCommitAndVerify(): void
    {
        $generatorG = EccFactory::getSecgCurves()->generator256k1();
        $generatorH = $generatorG->createPrivateKey()->getPoint();

        $random = RandomGeneratorFactory::getRandomGenerator();
        $randomR = $random->generate($generatorG->getOrder());
        $randomM = $random->generate($generatorG->getOrder());

        $commitment = new PedersonCommit($generatorG, $generatorH);
        $commitmentMessage = $commitment->commit($randomR, $randomM);

        $this->assertTrue($commitment->verify($commitmentMessage, $randomR, $randomM));
    }
}