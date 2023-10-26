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

readonly class BallotReceipt
{
    public function __construct(private BallotDigestSignature $ballotDigestSignature, private ?string $ballotVoterId)
    {
    }

    /**
     * @return array{
     * 'fingerprint': string,
     * 'signature': string,
     *   'ballotVoterId': ?string,
     * }
     */
    public function export(): array
    {
        $export = $this->ballotDigestSignature->export();

        return [
            ...$export,
            'ballotVoterId' => $this->ballotVoterId,
        ];
    }
}
