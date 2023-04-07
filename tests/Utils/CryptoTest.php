<?php

namespace Famoser\PolyasVerification\Test\Utils;

use Famoser\PolyasVerification\Crypto;
use PHPUnit\Framework\TestCase;

class CryptoTest extends TestCase
{
    public function testFingerprintVerifies(): void
    {
        $this->assertFalse(Crypto::verifyFingerprintSignature('', '', ''));
    }
}
