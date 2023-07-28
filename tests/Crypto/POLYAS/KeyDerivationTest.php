<?php

namespace Famoser\PolyasVerification\Test\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\POLYAS\KeyDerivation;
use PHPUnit\Framework\TestCase;

class KeyDerivationTest extends TestCase
{
    public function testKeyDerivation(): void
    {
        $keyDerivationResultHex = $this->getKeyDerivationResultHex();

        $keyDerivation = new KeyDerivation(65, 'kdk', 'label', 'context');
        $derivedKey = $keyDerivation->derive();

        $derivedKeyHex = bin2hex($derivedKey);
        $this->assertEquals($keyDerivationResultHex, $derivedKeyHex);
    }

    private function getKeyDerivationResultHex(): string
    {
        /** @var string $fileContent */
        $fileContent = file_get_contents(__DIR__.'/resources/keyDerivationResult.hex');

        return strtolower(preg_replace('/\s+/', '', $fileContent));
    }
}