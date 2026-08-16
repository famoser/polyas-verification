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
 * Used to test the receipt procedure without needing an active election.
 */
abstract readonly class ReceiptMock
{
    private const string FINGERPRINT = '1160ba5fc7878405b96516ed371e547b5f107ccdeea838aa577d4aea75c36ef2';
    private const string SIGNATURE = '44f547c20f60ce2a67dbd19c342530f836d4e1727573c38ca4844db73ae88b8495692cbd1d56db0724c56a9818f6a9e320fd473d560c892ffcaac9697ada6c3875578f6b00db89294088435e0914e4ab9508c173fe763fa5ca8726a8642d343fc801186861bca29271be2e88e149ed655aa7b12535f7728645f47fdd6e0244981e7699d94da466f6554314ac0fec5a83508143e1fb4d96efc69c5c7cc498a5917031f021a1011b8e923316116fc4ed98b3d097c4f23dfbf833ce6357c9b494b84cf11470cc885ccb089cedc841128174939e1dfd11a8468aba359aa5864e97086b0b31464913216b639d64a59d54113906dc9edae006648196c321d8b1eb0944f00dc717fa6abd71bf78f051b6893f7a9cf3bd5039d9a4e6bfa9bf9a2a10f6efd9d12c80410616ceeb8fe3ab0307e53d500e56e31da0d5c94fbcce7f663f9e60f48b0265a7d008718b7e3ad94b00d13cbd3c77bd66649f7e354227437124cd8db86c2c5e5a64ea5c9c7e9bb48c95944a1e68ad281ea2d1b034f14f03eec120d2';
    private const string BALLOT_VOTER_ID = 'voter7';

    protected const string VERIFICATION_KEY = '308201a2300d06092a864886f70d01010105000382018f003082018a0282018100db68673690d266f8ce7c0b718ca3be22f74a0ffe28ba1205bc68fe31677e422fa98b602870fa2d699df9c4f3a983a6fc08b93bae559b8b8e2c16483b24bc789066831ff4e063998590fca2e8f431f2a1716c1da6771377c1255e68d8334a160f8fe4c8a490d58675b24df04bfe6226d97e3a3af97cff761daf2d4ef8e7a9262335b4ab64222b841fb32a043bc454b65099092f432dcb3d2b3b76827555c18f7f7163cb4b7bae015d2e0007de08f6c00bfbf3f1087d291e3d4d5f7bf4b267213ea9f3e531aade52bb7084ab83f638075baa36133ad9eaf85e974c5ffe2709cc6286ce92a205c6b8f111169e7e71937741ae1983518388505943ff7ff858363fa6c6403c9e1d82e9b16fb69368895ebd800f68b46f9060ec533ccbf474d55b98d9d1f71fc8f7cf7149a0fba9e06226536394ea5902c7b1105c3cc22ce031edaace55130a0815fa293f5a55ea9d4f6c3f4db3ad0f843d63a8ea87db946f1ae26a81242424c03c0b71393948b436351ab5c7e6cf40c52816709afe1521f5c070b6770203010001';
    protected const string ELECTION_NAME = 'TEST_ELECTION';

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
}
