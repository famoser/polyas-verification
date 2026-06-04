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

class KeyFactory
{
    private const string ERROR_RANDOM_NUMBER_GENERATOR_NOT_FOUND = 'error:12000079:random number generator::Cannot open file';

    public static function generateRSAKey(int $keyBits, bool $allowUnsafeRandomness): \OpenSSLAsymmetricKey
    {
        $options = [
            'private_key_bits' => $keyBits,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        $pkey = @openssl_pkey_new($options);
        if (!$pkey) {
            throw OpenSSLException::createWithErrors('key generation failed');
        }
        OpenSSLException::throwIfErrors($allowUnsafeRandomness ? self::ERROR_RANDOM_NUMBER_GENERATOR_NOT_FOUND : '');

        return $pkey;
    }

    public static function getPublicKeyPem(\OpenSSLAsymmetricKey $key): string
    {
        $keyDetails = @openssl_pkey_get_details($key);
        if (!$keyDetails) {
            throw OpenSSLException::createWithErrors('key generation failed');
        }
        OpenSSLException::throwIfErrors();

        return $keyDetails['key'];
    }
}
