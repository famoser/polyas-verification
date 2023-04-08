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

class Key
{
    public static function generateRSAKey(int $keyBits): \OpenSSLAsymmetricKey
    {
        $options = [
            'private_key_bits' => $keyBits,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        $pkey = @openssl_pkey_new($options);
        if (!$pkey) {
            throw OpenSSLException::createWithErrors('key generation failed');
        }
        OpenSSLException::throwIfErrors();

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
