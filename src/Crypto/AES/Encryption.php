<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\AES;

use Famoser\PolyasVerification\Crypto\RSA\OpenSSLException;

class Encryption
{
    public static function encryptECB(string $data, string $key): string
    {
        $output = openssl_encrypt($data, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
        if (false === $output) {
            throw OpenSSLException::createWithErrors('Encryption failed');
        }

        OpenSSLException::throwIfErrors();

        return $output;
    }

    public static function decryptECB(string $data, string $key): string
    {
        $output = openssl_decrypt($data, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
        if (false === $output) {
            throw OpenSSLException::createWithErrors('Decryption failed');
        }

        OpenSSLException::throwIfErrors();

        return $output;
    }
}
