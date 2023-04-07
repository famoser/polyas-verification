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

use Famoser\PolyasVerification\Crypto;
use PHPUnit\Framework\TestCase;

class CryptoTest extends TestCase
{
    public const TEST_RSA_KEY_BITS = 1024; // DANGER: Too small size for production use.
    /**
     * @throws \Exception
     */
    public function testFingerprintVerifies(): void
    {
        $data = 'some data, still needs to be hashed';

        $key = Crypto::generateRSAKey(self::TEST_RSA_KEY_BITS);
        $signature = Crypto::signSha256($data, $key);
        $publicKey = Crypto::getPublicKeyPem($key);

        $this->assertTrue(Crypto::verifyRSASHA256Signature($data, $signature, $publicKey));
        $this->assertFalse(Crypto::verifyRSASHA256Signature(hash('sha256', $data), $signature, $publicKey));
        $this->assertFalse(Crypto::verifyRSASHA256Signature($data, hash('sha256', $signature), $publicKey));
        $this->assertFalse(Crypto::verifyRSASHA256Signature($data, $signature, hash('sha256', $publicKey)));
    }
}
