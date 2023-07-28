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

class QRCode
{
    /**
     * @param array{
     *     'c': string,
     *     'vid': string,
     *     'nonce': string
     *     } $content
     */
    public function __construct(private array $content)
    {
    }

    public function decrypt(BallotDigest $ballotEntry, string $comSeed)
    {
        $hashBallot = $ballotEntry->createFingerprint();
        $keyDerivationKey = $comSeed.$hashBallot;
    }
}
