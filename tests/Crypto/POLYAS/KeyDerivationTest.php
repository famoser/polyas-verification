<?php

namespace Famoser\PolyasVerification\Test\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\POLYAS\KeyDerivation;
use PHPUnit\Framework\TestCase;

class KeyDerivationTest extends TestCase
{
    public function testKeyDerivation(): void
    {
        $keyDerivation = new KeyDerivation('kdk', 65, 'label', 'context');
        $derivedKey = $keyDerivation->derive();

        $derivedKeyHex = bin2hex($derivedKey);
        $this->assertEquals('3288922a966533c793ed532045fffc3ce6ba77f27e8f60c9a3d82221d86f51dda00736dba3f8ae1d94b17562e838d57fb85400d147c6e9585ed4d859e46120b275', $derivedKeyHex);
    }
}
