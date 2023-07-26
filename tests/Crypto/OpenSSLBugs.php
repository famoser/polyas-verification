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

use PHPUnit\Framework\TestCase;

class OpenSSLBugs extends TestCase
{
    public function testPEMErrorUsingGetPublickey(): void
    {
        $data = 'some string';

        $key = openssl_pkey_new();
        $publicKeyPem = openssl_pkey_get_details($key)['key']; // @phpstan-ignore-line
        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256); // @phpstan-ignore-line

        // BUG: parsing the public key is successful, but an error is logged nonetheless
        $publicKey = openssl_get_publickey($publicKeyPem);
        $this->assertNotEquals(false, $publicKey); // publicKey is extracted successfully (you may also check by passing this to openssl_verify
        $this->assertEquals('error:0480006C:PEM routines::no start line', openssl_error_string());
        $this->assertFalse(openssl_error_string());
    }

    public function testPEMErrorUsingSign(): void
    {
        $data = 'some string';

        $key = openssl_pkey_new();
        $publicKeyPem = openssl_pkey_get_details($key)['key']; // @phpstan-ignore-line
        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256); // @phpstan-ignore-line

        // BUG: Verifying the signature is successful, but an error is logged nonetheless
        $result = openssl_verify($data, $signature, $publicKeyPem, 'sha256WithRSAEncryption');
        $this->assertEquals(1, $result); // signature OK
        $this->assertEquals('error:0480006C:PEM routines::no start line', openssl_error_string());
        $this->assertFalse(openssl_error_string());
    }
}
