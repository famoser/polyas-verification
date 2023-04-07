<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification;

class Crypto
{
    public static function verifyRSASHA256Signature(string $data, string $signature, string $publicKey): bool
    {
        if (openssl_verify($data, $signature, $publicKey, 'sha256WithRSAEncryption')) {
            return true;
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    public static function generateRSAKey(int $keyBits): \OpenSSLAsymmetricKey
    {
        $options = [
            'private_key_bits' => $keyBits,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        $pkey = openssl_pkey_new($options);
        if (!$pkey) {
            throw new \Exception('key generation failed');
        }

        return $pkey;
    }

    /**
     * @throws \Exception
     */
    public static function getPublicKeyPem(\OpenSSLAsymmetricKey $key): string
    {
        $keyDetails = openssl_pkey_get_details($key);
        if (!$keyDetails) {
            throw new \Exception('failed to get key details');
        }

        return $keyDetails['key'];
    }

    /**
     * @throws \Exception
     */
    public static function signSha256(string $data, \OpenSSLAsymmetricKey $key): string
    {
        if (!openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('signature failed');
        }

        return $signature;
    }
}
