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
use Famoser\PolyasVerification\Workflow\StoreReceipt;

/**
 * Used to test the verification procedure without needing an active election.
 */
readonly class StoreReceiptMock extends ReceiptMock
{
    /**
     * @param array{
     * 'fingerprint': string,
     * 'signature': string,
     * 'ballotVoterId': string,
     * } $payload
     */
    public static function performMockStoreReceipt(array $payload, ?string &$failedCheck = null): bool
    {
        $storeReceipt = new StoreReceipt(self::VERIFICATION_KEY, self::ELECTION_NAME);

        return $storeReceipt->store($payload, $failedCheck);
    }
}
