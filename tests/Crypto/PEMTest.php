<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Crypto;

use Famoser\PolyasVerification\Crypto\PEM;
use Famoser\PolyasVerification\Crypto\RSA;
use PHPUnit\Framework\TestCase;

class PEMTest extends TestCase
{
    public function testExtractPayloadsPublicKeyPem(): void
    {
        $publicKeyPem = $this->getRSAPublicKeyAsPEM();

        $payloads = PEM\Decoder::decode($publicKeyPem);
        $this->assertCount(1, $payloads);
        $this->assertEquals('PUBLIC KEY', $payloads[0]->getLabel());
    }

    public function testEncoderReversesDecoding(): void
    {
        $publicKeyPem = $this->getRSAPublicKeyAsPEM();
        $payloads = PEM\Decoder::decode($publicKeyPem);

        $publicKeyPem2 = PEM\Encoder::encode($payloads[0]->getLabel(), $payloads[0]->getPayload());
        $this->assertEquals($publicKeyPem, $publicKeyPem2);
    }

    private function getRSAPublicKeyAsPEM(): string
    {
        $key = RSA\KeyFactory::generateRSAKey(RSATest::TEST_RSA_KEY_BITS);

        return RSA\KeyFactory::getPublicKeyPem($key);
    }
}
