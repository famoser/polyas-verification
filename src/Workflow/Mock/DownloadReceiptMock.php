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
class DownloadReceiptMock
{
    private const FINGERPRINT = 'bd9941bf426f9b0a4430fbed2a53cc5d250c4103a573e56a015b44dc45b8156a';
    private const SIGNATURE = '607340deaddbf380e08097b9dd085702334264c6f090e4f885d2e5c94aeb746946792ceb72f3ff2ca6e7919f001cad3a81927e8ad075a43fcc77d55b8215489853691cb201b5893db12e6b4127b59e14c7c3c8463bf86595e6cf2e64486de9530156ed3e0d55e60c5f3ff017c141409789d4142a6259120b0d4fde976e346e33145fd6fffa9419608ac7678e88e6aa07b3e24db97079a4db6bfe348b4554c55d215eb9b040907965928d07addd67ebfdf1c1c752e5325adba434a001ed26aa86638d00465c2d6e693767ff8ad6e974eed35c2aaa6615d794968b0aab2e2ae4ae18c55a18eb4e9178b12125373159da0196e264699e499e69058e68a40a81be50';
    private const BALLOT_VOTER_ID = '02065f4b8b3d3304865a75971926951885a774b1d4867ee4a068c6407f234659c2';

    private const VERIFICATION_KEY = '30820122300d06092a864886f70d01010105000382010f003082010a02820101008a2af5d4fb9fed019a706d110efac4f658ae2826bc5134cb58ef7994dd5812e9c7cd6f6a6577e940e3b0b6c8b7b13c3b29d3bbca5da02d4bcbf6feff86ac7a640ef263afec49dcdecea05483e54cbd1098fd8d8a7d040d347f874cc2dccfe0df7154e71665a8ddc32e3b739c141fc55b909032acc57bf40994b719161305d9f8c2f9c4da52db7b734cb9f06e9546f067eb9d5842fe57b2e7343660b85f3a4688abd8367c2377812aa8dfdd169fae08bf0ccddd3e4ad17dd934636f64def7a7f7467b64ff35b2bc39f75970c982bac2cd0bac64b553c47edda6f68ac66d715bb9da39f50804f57d1eef77f6ce1fe252c1e235a96e473258ddb9f34818e35b3b6b0203010001';
    private const ELECTION_NAME = 'TEST_ELECTION';

    /**
     * @return array{
     *      'fingerprint': string,
     *      'signature': string,
     *      'ballotVoterId': string,
     *  }
     */
    public static function createMockPayload(): array
    {
        return [
            'fingerprint' => self::FINGERPRINT,
            'signature' => self::SIGNATURE,
            'ballotVoterId' => self::BALLOT_VOTER_ID,
        ];
    }

    /**
     * @param array{
     *       'fingerprint': string,
     *       'signature': string,
     *       'ballotVoterId': string,
     * } $payload
     */
    public static function isMockPayload(array $payload): bool
    {
        return self::FINGERPRINT === $payload['fingerprint']
            && self::SIGNATURE === $payload['signature']
            && self::BALLOT_VOTER_ID === $payload['ballotVoterId'];
    }

    /**
     * @param array{
     * 'fingerprint': string,
     * 'signature': string,
     * 'ballotVoterId': string,
     * } $payload
     */
    public static function performMockDownloadReceipt(array $payload, ?string &$pdf = null, ?string &$failedCheck = null): bool
    {
        $storeReceipt = new DownloadReceipt(self::VERIFICATION_KEY, self::ELECTION_NAME);

        return $storeReceipt->store($payload, $pdf, $failedCheck);
    }
}
