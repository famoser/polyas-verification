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

use Famoser\PolyasVerification\Crypto\RSA;
use PHPUnit\Framework\TestCase;

class RSATest extends TestCase
{
    public const TEST_RSA_KEY_BITS = 1024; // DANGER: Too small size for production use.

    /**
     * @return array{string, string}
     */
    private function createValidSignature(string $data): array
    {
        $key = RSA\KeyFactory::generateRSAKey(self::TEST_RSA_KEY_BITS);
        $publicKey = RSA\KeyFactory::getPublicKeyPem($key);

        $signature = RSA\Signature::signSha256($data, $key);

        return [$publicKey, $signature];
    }

    public function testVerifySHA256ValidSignatureThrowsNot(): void
    {
        $data = 'some data';
        [$publicKey, $signature] = $this->createValidSignature($data);

        $this->expectNotToPerformAssertions();
        RSA\Signature::verifySHA256($data, $signature, $publicKey);
    }

    public function testVerifySHA256InvalidSignatureThrows(): void
    {
        $data = 'some data';
        [$publicKey, $signature] = $this->createValidSignature($data);

        $wrongData = hash('sha256', $data);
        $this->expectException(RSA\OpenSSLException::class);
        RSA\Signature::verifySHA256($wrongData, $signature, $publicKey);
    }

    public function testVerifySHA256InvalidSignatureThrows1(): void
    {
        $data = 'some data';
        [$publicKey, $signature] = $this->createValidSignature($data);

        $wrongSignature = hash('sha256', $signature);
        $this->expectException(RSA\OpenSSLException::class);
        RSA\Signature::verifySHA256($data, $wrongSignature, $publicKey);
    }

    public function testVerifySHA256InvalidSignatureThrows2(): void
    {
        $data = 'some data';
        [$publicKey, $signature] = $this->createValidSignature($data);

        $wrongPublicKey = hash('sha256', $publicKey);
        $this->expectException(RSA\OpenSSLException::class);
        RSA\Signature::verifySHA256($data, $signature, $wrongPublicKey);
    }
}
