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

    public function createComKey(): string
    {
        $hashBallot = $this->ballotDigest->createFingerprint();
        /** @var string $comSeed */
        $comSeed = hex2bin($this->comSeedHex);

        $keyDerivationKey = $comSeed.$hashBallot;
        $keyDerivation = new KeyDerivation($keyDerivationKey, 32, '', '');

        return $keyDerivation->derive();
    }

    public function decrypt(): string
    {
        $data = base64_decode($this->QRCode->getCBase64());
        $comKey = $this->createComKey();

        return AES\Encryption::decryptECB($data, $comKey);
    }
}
