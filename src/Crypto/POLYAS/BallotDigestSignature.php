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
use Famoser\PolyasVerification\Crypto\RSA\OpenSSLException;

readonly class BallotDigestSignature
{
    public function __construct(private string $fingerprint, private string $signature, private string $verificationKeyX509)
    {
    }

    public static function createFromBallotDigest(BallotDigest $ballotDigest, string $signatureHex, string $verificationKeyX509Hex): self
    {
        /** @var string $signature */
        $signature = hex2bin($signatureHex);
        /** @var string $verificationKeyX509 */
        $verificationKeyX509 = hex2bin($verificationKeyX509Hex);

        return new self($ballotDigest->createFingerprint(), $signature, $verificationKeyX509);
    }

    public function verify(): bool
    {
        $publicKeyPem = PEM\Encoder::encode('PUBLIC KEY', $this->verificationKeyX509);

        try {
            return RSA\Signature::verifySHA256($this->fingerprint, $this->signature, $publicKeyPem);
        } catch (OpenSSLException) {
            return false;
        }
    }
}
