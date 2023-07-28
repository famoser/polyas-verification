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

class QRCodeDecryption
{
    public function __construct(private $QRCode, private BallotDigest $ballotEntry, private string $comSeed)
    {
    }

    public function getComKey()
    {
        $hashBallot = $this->ballotEntry->createFingerprint();
        $keyDerivationKey = $this->comSeed.$hashBallot;
    }
}
