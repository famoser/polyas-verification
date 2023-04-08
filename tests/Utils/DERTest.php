<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Utils;

use Famoser\PolyasVerification\Crypto\DER;
use Famoser\PolyasVerification\Crypto\PEM;
use Famoser\PolyasVerification\Crypto\RSA;
use PHPUnit\Framework\TestCase;

class DERTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testExtractPayloadsPublicKeyPem(): void
    {
        $rsaPublicKeyAsDER = $this->getRSAPublicKeyAsDER();
        $rsaPublicKey = DER\Decoder::asRSAPublicKey($rsaPublicKeyAsDER);

        $nBitLength = strlen(gmp_strval($rsaPublicKey->getN(), 2));
        $this->assertEquals(1024, $nBitLength);

        $eBitLength = strlen(gmp_strval($rsaPublicKey->getE(), 2));
        $this->assertEquals(17, $eBitLength);
    }

    private function getRSAPublicKeyAsDER(): string
    {
        $key = RSA\Key::generateRSAKey(RSATest::TEST_RSA_KEY_BITS);
        $publicKey = RSA\Key::getPublicKeyPem($key);
        $payloads = PEM\Decoder::decode($publicKey);

        return $payloads[0]->getPayload();
    }
}
