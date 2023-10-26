<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Workflow;

use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigestSignature;
use Famoser\PolyasVerification\Storage;

readonly class StoreReceipt
{
    public const RECEIPT_VALID = 'RECEIPT_VALID';
    public const RECEIPT_UNIQUE = 'RECEIPT_UNIQUE';
    public const RECEIPT_STORED = 'RECEIPT_STORED';

    public function __construct(private string $verificationKeyX509Hex, private string $polyasElection)
    {
    }

    /**
     * @param array{
     * 'fingerprint': string,
     *  'signature': string,
     *  'ballotVoterId': ?string,
     * } $receipt
     */
    public function store(array $receipt, string &$failedCheck = null): bool
    {
        $ballotSignature = BallotDigestSignature::createFromExport($receipt, $this->verificationKeyX509Hex);
        if (!$ballotSignature->verify()) {
            $failedCheck = self::RECEIPT_VALID;

            return false;
        }

        if (Storage::checkReceiptExists($receipt)) {
            $failedCheck = self::RECEIPT_UNIQUE;

            return true;
        }

        if (!Storage::storeReceipt($receipt, $this->polyasElection)) {
            $failedCheck = self::RECEIPT_STORED;

            return false;
        }

        return true;
    }
}
