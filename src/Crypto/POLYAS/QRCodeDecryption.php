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

readonly class QRCodeDecryption
{
    public function __construct(private QRCode $QRCode, private BallotDigest $ballotDigest, private string $comSeedHex)
    {
    }

    public function getContent(): string
    {
        $comKey = $this->createComKey();

        return $this->decrypt($comKey);
    }

    public function createComKey(): string
    {
        $hashBallot = $this->ballotDigest->createFingerprint();
        /** @var string $comSeed */
        $comSeed = hex2bin($this->comSeedHex);

        $keyDerivationKey = $comSeed.$hashBallot;
        $keyDerivation = new KeyDerivation($keyDerivationKey, 32, '', '');

        return $keyDerivation->derive();
    }

    public function decrypt(string $comKey): string
    {
        $base64 = Base64UrlEncoding::decode($this->QRCode->getCBase64UrlSafe());
        $data = base64_decode($base64);
        $iv = substr($data, 0, 12);
        $tag = substr($data, 12, 16);
        $ciphertext = substr($data, 28);

        return AES\Encryption::decryptGCM($ciphertext, $comKey, $iv, $tag);
    }
}
