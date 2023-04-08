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

use PHPUnit\Framework\TestCase;

class OpenSSLBugs extends TestCase
{
    public function testPEMError(): void
    {
        $data = 'some string';

        $key = openssl_pkey_new();
        $publicKey = openssl_pkey_get_details($key)['key']; // @phpstan-ignore-line
        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256); // @phpstan-ignore-line
        openssl_verify($data, $signature, $publicKey, 'sha256WithRSAEncryption');

        $this->assertEquals('error:0480006C:PEM routines::no start line', openssl_error_string());
        $this->assertFalse(openssl_error_string());
    }
}
