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

use Famoser\PolyasVerification\Crypto\PEM;
use Famoser\PolyasVerification\Crypto\RSA;

readonly class BallotEntrySignature
{
    public function __construct(private BallotEntry $ballotEntry, private string $signatureHex, private string $verificationKeyX509Hex)
    {
    }

    public function verify(): void
    {
        $data = $this->ballotEntry->createDigestBin();

        /** @var string $verificationKeyBin */
        $verificationKeyBin = hex2bin($this->verificationKeyX509Hex);
        $publicKeyPem = PEM\Encoder::encode('PUBLIC KEY', $verificationKeyBin);

        /** @var string $signature */
        $signature = hex2bin($this->signatureHex);
        RSA\Signature::verifySHA256($data, $signature, $publicKeyPem);
    }
}
