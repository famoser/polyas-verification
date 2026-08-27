<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Workflow\Mock;

use Famoser\PolyasVerification\Workflow\DownloadReceipt;

/**
 * Used to test the verification procedure without needing an active election.
 */
readonly class DownloadReceiptMock extends ReceiptMock
{
    /**
     * @param array{
     * 'fingerprint': string,
     * 'signature': string,
     * } $payload
     */
    public static function performMockDownloadReceipt(array $payload, ?string &$pdf = null, ?string &$failedCheck = null): bool
    {
        $storeReceipt = new DownloadReceipt(self::VERIFICATION_KEY, self::ELECTION_NAME);

        return $storeReceipt->store($payload, $pdf, $failedCheck);
    }
}
