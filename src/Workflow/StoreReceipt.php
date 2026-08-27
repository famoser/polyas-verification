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

/**
 * @phpstan-type ValidReceipt array{
 *     fingerprint: string,
 *     signature: string,
 * }
 */
readonly class StoreReceipt
{
    public const string SIGNATURE_VALID = 'SIGNATURE_VALID';
    public const string RECEIPT_STORED = 'RECEIPT_STORED';

    public function __construct(private string $verificationKeyX509Hex, private string $polyasElection)
    {
    }

    /**
     * @param ValidReceipt $validReceipt
     */
    public function store(array $validReceipt, ?string &$failedCheck = null): bool
    {
        /** @var string $fingerprint */
        $fingerprint = hex2bin($validReceipt['fingerprint']);
        /** @var string $signature */
        $signature = hex2bin($validReceipt['signature']);
        /** @var string $verificationKeyX509 */
        $verificationKeyX509 = hex2bin($this->verificationKeyX509Hex);
        $ballotSignature = new BallotDigestSignature($fingerprint, $signature, $verificationKeyX509);
        if (!$ballotSignature->verify()) {
            $failedCheck = self::SIGNATURE_VALID;

            return false;
        }

        $validReceipt = $ballotSignature->export();
        // only store if it does not exist yet
        if (!Storage::checkReceiptExists($validReceipt) && !Storage::storeReceipt($validReceipt, $this->polyasElection)) {
            $failedCheck = self::RECEIPT_STORED;

            return false;
        }

        return true;
    }
}
