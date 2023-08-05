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
    public function __construct(private string $payload, private BallotDigest $ballotDigest, private string $comSeed)
    {
    }

    public function decrypt(): ?string
    {
        $comKey = $this->createComKey();

        try {
            return $this->performDecryption($comKey);
        } catch (OpenSSLException) {
            return null;
        }
    }

    public function createComKey(): string
    {
        $hashBallot = $this->ballotDigest->createNorm();
        $comSeed = $this->comSeed;

        $keyDerivationKey = $comSeed.$hashBallot;
        $keyDerivation = new KeyDerivation($keyDerivationKey, 32, '', '');

        return $keyDerivation->derive();
    }

    public function performDecryption(string $comKey): string
    {
        $base64 = Base64UrlEncoding::decode($this->payload);
        $data = base64_decode($base64);
        $iv = substr($data, 0, 12);
        $tag = substr($data, 12, 16);
        $ciphertext = substr($data, 28);

        return AES\Encryption::decryptGCM($ciphertext, $comKey, $iv, $tag);
    }
}
