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

use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use Famoser\PolyasVerification\Workflow\Verification;

/**
 * Used to test the verification procedure without needing an active election.
 */
class VerificationMock
{
    private const PAYLOAD = '7bgIHYQotKLc8tgCbWp5yuc83xSbN-JV4Vwpnb50qyIzNUj2tYDYzPInG80WJ1mf2tB8BstZXWH_b0y4';
    private const VOTER_ID = 'voter3';
    private const NONCE = 'f299af96450db626754147aa132237bbf5603df2eea8215a0859288df8015c85';
    private const PASSWORD = '123456';
    private const CHALLENGE = '96915244934611756885814581302818582006089481594800375652902059530399338848463';
    private const CHALLENGE_RANDOM_COIN = '56527814749137956895630027000199810756343224065985652099532394141657865428867';
    private const DEVICE_PARAMETERS_JSON = '{"publicKey":"03249beb1a187aa1a8ec37969b67aba6a6e8eca89890c8c0456859a6ca86439944","verificationKey":"30820122300d06092a864886f70d01010105000382010f003082010a02820101008a2af5d4fb9fed019a706d110efac4f658ae2826bc5134cb58ef7994dd5812e9c7cd6f6a6577e940e3b0b6c8b7b13c3b29d3bbca5da02d4bcbf6feff86ac7a640ef263afec49dcdecea05483e54cbd1098fd8d8a7d040d347f874cc2dccfe0df7154e71665a8ddc32e3b739c141fc55b909032acc57bf40994b719161305d9f8c2f9c4da52db7b734cb9f06e9546f067eb9d5842fe57b2e7343660b85f3a4688abd8367c2377812aa8dfdd169fae08bf0ccddd3e4ad17dd934636f64def7a7f7467b64ff35b2bc39f75970c982bac2cd0bac64b553c47edda6f68ac66d715bb9da39f50804f57d1eef77f6ce1fe252c1e235a96e473258ddb9f34818e35b3b6b0203010001","ballots":[{"type":"STANDARD_BALLOT","id":"1","contentAbove":{"value":{"default":"","value":{}},"contentType":"TEXT"},"title":{"default":"Wie findest du Vanilleeis?","value":{}},"lists":[{"id":"2","columnHeaders":[{"default":"","value":{}}],"columnProperties":[{"hide":false}],"candidates":[{"id":"3","columns":[{"value":{"default":"Lecker!","value":{}},"contentType":"TEXT"}],"maxVotes":1,"minVotes":0},{"id":"4","columns":[{"value":{"default":"Es gibt besseres","value":{}},"contentType":"TEXT"}],"maxVotes":1,"minVotes":0}],"maxVotesOnList":1,"minVotesOnList":0,"maxVotesForList":0,"minVotesForList":0,"voteCandidateXorList":false}],"contentBelow":{"value":{"default":"","value":{}},"contentType":"TEXT"},"showInvalidOption":true,"showAbstainOption":false,"maxVotes":1,"minVotes":0,"maxVotesForLists":0,"minVotesForLists":0,"prohibitMoreVotes":false,"prohibitLessVotes":false,"calculateAvailableVotes":false}]}';

    /**
     * @param array{
     *     'payload': string,
     *     'voterId': string,
     *     'nonce': string,
     *     'password': string,
     * } $payload
     */
    public static function isMockPayload(array $payload): bool
    {
        return self::PAYLOAD === $payload['payload']
            && self::VOTER_ID === $payload['voterId']
            && self::NONCE === $payload['nonce']
            && self::PASSWORD === $payload['password'];
    }

    /**
     * @param array{
     * 'fingerprint': string,
     * 'signature': string,
     * }|null $validReceipt
     */
    public static function performMockVerification(string &$failedCheck = null, array &$validReceipt = null): string|null
    {
        $apiClient = new VerificationMockApiClient();
        $verification = new Verification(self::DEVICE_PARAMETERS_JSON, $apiClient);

        $challenge = gmp_init(self::CHALLENGE, 10);
        $challengeRandomCoin = gmp_init(self::CHALLENGE_RANDOM_COIN, 10);
        $challengeCommit = new ChallengeCommit($challenge, $challengeRandomCoin);
        $payload = [
            'payload' => self::PAYLOAD,
            'voterId' => self::VOTER_ID,
            'nonce' => self::NONCE,
            'password' => self::PASSWORD,
        ];

        return $verification->verify($payload, $challengeCommit, $failedCheck, $validReceipt);
    }
}
