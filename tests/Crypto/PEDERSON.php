<?php

namespace Famoser\PolyasVerification\Test\Crypto;

use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\MathFactory;
use Famoser\PolyasVerification\Crypto\Elliptic\RandomnessGenerator;
use Famoser\PolyasVerification\Crypto\PEDERSON\PedersonCommit;
use PHPUnit\Framework\TestCase;

class PEDERSON extends TestCase
{
    public function testCommitAndVerify(): void
    {
        $repository = new CurveRepository();
        $curve = $repository->findByName('secp256k1');
        if (!$curve) {
            $this->fail('Curve not found');
        }

        $factory = new MathFactory($repository);
        $math = $factory->createHardenedMath($curve);
        if (!$math) {
            $this->fail('Math not found');
        }

        $randomGenerator = new RandomnessGenerator($math);
        $generatorG = $randomGenerator->point();
        $generatorH = $randomGenerator->point();

        $randomR = $randomGenerator->scalar();
        $randomM = $randomGenerator->scalar();

        $commitment = new PedersonCommit($generatorG, $generatorH);
        $commitmentMessage = $commitment->commit($math, $randomR, $randomM);

        $this->assertTrue($commitment->verify($math, $commitmentMessage, $randomR, $randomM));
    }
}
