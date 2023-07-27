<?php

namespace Famoser\PolyasVerification\Test\Crypto;

use Famoser\PolyasVerification\Crypto\SECP256K1\Encoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SECP256K1 extends TestCase
{
    /**
     * @return string[][]
     */
    public static function compressedPointsProvider(): array
    {
        return [
            ['0373744f99d31509eb5f8caaabc0cc3fab70e571a5db4d762020723b9cd6ada260'],
            ['02549196EA21197151C73C3C9BDA1F12DA2BBEA99F2EFB0DD8BC235A9CED37ECB9'],
            ['03F08FCB284F32B737E0529840334D481E055AD6AFA18AB91A3B02939EB19EB8DD'],
            ['034A90A88BD2D3A92D7A29D19135F25536516D46FE4B8776C74B9E26D834FDA588'],
            ['0365DB947FD33BE257599D9E0BD1513E6F7B3BBE6C9008382E22F4B527D3A39299'],
            ['031E9073F6821FADE4307507F0D2756EFAAA4522FC15391390C943F4F4D9F32CE5'],
            ['030e1a9be2459151057e9d731b524ca435f1c05bc0a95d3d82b30512d306172b17'],
        ];
    }

    #[DataProvider('compressedPointsProvider')]
    public function testParseSpecifiedKey(string $predefinedGenerator): void
    {
        $generatorPoint = Encoder::parseCompressedPoint($predefinedGenerator);
        $compressedAgain = Encoder::compressPoint($generatorPoint);

        $this->assertEquals(strtolower($predefinedGenerator), $compressedAgain);
    }
}