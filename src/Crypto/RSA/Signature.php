<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\RSA;

class Signature
{
    public static function verifySHA256(string $data, string $signature, string $publicKey): bool
    {
        $result = @openssl_verify($data, $signature, $publicKey, 'sha256WithRSAEncryption');
        OpenSSLException::throwIfErrors();

        return 1 === $result;
    }

    public static function signSHA256(string $data, \OpenSSLAsymmetricKey $key): string
    {
        if (!@openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw OpenSSLException::createWithErrors('signing failed');
        }
        OpenSSLException::throwIfErrors();

        return $signature;
    }
}
