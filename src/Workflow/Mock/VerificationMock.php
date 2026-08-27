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
use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Workflow\Verification;

/**
 * Used to test the verification procedure without needing an active election.
 */
readonly class VerificationMock
{
    private const string ENC_C = 'J8kdvzMZHPQBHbVoNx+JrstXlgbhOqDIikjbOGOL9HvWVdRPWuw/Xbvq/nKS/mqH/h2FJjCYbozkJiq7';
    private const string ENC_D = '/EhRgDjIA+scXsSyfSXvqPCHsvbf/UozQicLbNd4bjkps8aP4ZXdo3R+KuvYX/ZM8NeAJcGrZbeb3wm8fgnby1gQJGqJwMY+eN6qXN83b0i5pNaej1WrMglE4KIXpDc8Bn00stxsvy0qlw==';
    private const string VOTER_ID = 'voter7';
    private const string NONCE = 'e552502592f5bec54e4750c769ae9a3ec913c69a7cd828ce0226201476a2f833';
    private const string PASSWORD = '123456';
    private const string CHALLENGE = '43030445049487747541029726235731232721292906839186252755320709847982812472754';
    private const string CHALLENGE_RANDOM_COIN = '26575495741935064455070503300995101427899481700480831329969424882911236540902';
    private const string DEVICE_PARAMETERS_JSON = '{"publicKey":"0390c059a207899b2dd76d5f5b7f40c73a02e620b67a5a23cc07134c3462b659aa","verificationKey":"308201a2300d06092a864886f70d01010105000382018f003082018a0282018100db68673690d266f8ce7c0b718ca3be22f74a0ffe28ba1205bc68fe31677e422fa98b602870fa2d699df9c4f3a983a6fc08b93bae559b8b8e2c16483b24bc789066831ff4e063998590fca2e8f431f2a1716c1da6771377c1255e68d8334a160f8fe4c8a490d58675b24df04bfe6226d97e3a3af97cff761daf2d4ef8e7a9262335b4ab64222b841fb32a043bc454b65099092f432dcb3d2b3b76827555c18f7f7163cb4b7bae015d2e0007de08f6c00bfbf3f1087d291e3d4d5f7bf4b267213ea9f3e531aade52bb7084ab83f638075baa36133ad9eaf85e974c5ffe2709cc6286ce92a205c6b8f111169e7e71937741ae1983518388505943ff7ff858363fa6c6403c9e1d82e9b16fb69368895ebd800f68b46f9060ec533ccbf474d55b98d9d1f71fc8f7cf7149a0fba9e06226536394ea5902c7b1105c3cc22ce031edaace55130a0815fa293f5a55ea9d4f6c3f4db3ad0f843d63a8ea87db946f1ae26a81242424c03c0b71393948b436351ab5c7e6cf40c52816709afe1521f5c070b6770203010001","ballots":[{"type":"STANDARD_BALLOT","id":"A","title":{"default":"Ballot title","value":{}},"lists":[{"id":"A1","title":{"default":"First question!","value":{}},"columnHeaders":[{"default":"","value":{}}],"candidates":[{"id":"A1-1","columns":[{"value":{"default":"Yes","value":{}},"contentType":"TEXT"}],"maxVotes":1,"minVotes":0},{"id":"A1-2","columns":[{"value":{"default":"No","value":{}},"contentType":"TEXT"}],"maxVotes":1,"minVotes":0}],"maxVotesOnList":1,"minVotesOnList":1,"maxVotesForList":0,"minVotesForList":0,"voteCandidateXorList":false}],"showInvalidOption":true,"showAbstainOption":false,"maxVotes":1,"minVotes":0,"prohibitMoreVotes":false,"prohibitLessVotes":false,"calculateAvailableVotes":false,"voterClientSettings":{"calculateAvailableVotes":false,"hideVoteCountForLists":false,"showInvalidOption":true,"showAbstainOption":false,"prohibitMoreVotes":false,"prohibitLessVotes":false}}]}';

    /**
     * @return array{
     *      'c': string,
     *      'd': string,
     *       'vid': string,
     *       'nonce': string,
     *      'password': string,
     *  }
     */
    public static function createMockPayload(): array
    {
        return [
            'c' => self::ENC_C,
            'd' => self::ENC_D,
            'vid' => self::VOTER_ID,
            'nonce' => self::NONCE,
            'password' => self::PASSWORD,
        ];
    }

    /**
     * @param array{
     *  'c': string,
     *  'd': string,
     *  'vid': string,
     *  'nonce': string,
     *     'password': string,
     * } $payload
     */
    public static function isMockPayload(array $payload): bool
    {
        return self::ENC_C === $payload['c']
            && self::ENC_D === $payload['d']
            && self::VOTER_ID === $payload['vid']
            && self::NONCE === $payload['nonce']
            && self::PASSWORD === $payload['password'];
    }

    /**
     * @param array{
     *     'c': string,
     *     'd': string,
     *     'vid': string,
     *     'nonce': string
     * } $payload
     * @param array{
     * 'fingerprint': string,
     * 'signature': string,
     * }|null $validReceipt
     */
    public static function performMockVerification(array $payload, string $password, ?string &$failedCheck = null, ?array &$validReceipt = null, ?string &$hexBallot = null): bool
    {
        $apiClient = new VerificationMockApiClient();
        $deviceParameters = new DeviceParameters(self::DEVICE_PARAMETERS_JSON);
        $verification = new Verification($deviceParameters, $apiClient);

        $challenge = gmp_init(self::CHALLENGE, 10);
        $challengeRandomCoin = gmp_init(self::CHALLENGE_RANDOM_COIN, 10);
        $challengeCommit = new ChallengeCommit($challenge, $challengeRandomCoin);

        return $verification->verify($payload, $password, $challengeCommit, $validReceipt, $hexBallot, $failedCheck);
    }
}
