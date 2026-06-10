<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\AES;
use Famoser\PolyasVerification\Crypto\RSA\OpenSSLException;

readonly class QRCodeDecryption
{
    public function __construct(private string $c, private string $d, private string $comSeed)
    {
    }

    /**
     * @phpstan-assert-if-true string $randomCoinSeed
     * @phpstan-assert-if-true string $referenceCoin
     */
    public function decrypt(?string &$randomCoinSeed = null, ?string &$referenceCoin = null): bool
    {
        $comKey = $this->createComKey();

        try {
            $randomCoinSeed = $this->decryptValue($comKey, $this->c);
            $referenceCoin = $this->decryptValue($comKey, $this->d);

            return true;
        } catch (OpenSSLException) {
            return false;
        }
    }

    public function createComKey(): string
    {
        $keyDerivation = new KeyDerivation($this->comSeed, 32, '', '');

        return $keyDerivation->derive();
    }

    public static function decryptValue(string $comKey, string $value): string
    {
        $data = base64_decode($value);
        $iv = substr($data, 0, 12);
        $ciphertext = substr($data, 12, -16);
        $tag = substr($data, strlen($data) - 16);

        return AES\Encryption::decryptGCM($ciphertext, $comKey, $iv, $tag);
    }
}
