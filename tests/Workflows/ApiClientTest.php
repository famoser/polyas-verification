<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Workflows;

use Famoser\PolyasVerification\Crypto\POLYAS\Base64UrlEncoding;
use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use Famoser\PolyasVerification\Workflow\ApiClient;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    use IncompleteTestTrait;

    public function testReceiptVerify(): void
    {
        $this->markTestIncompleteNS('Requires live server.');

        $apiClient = new ApiClient('https://election.polyas.com/59e875f6-2d11-466a-8a49-d7f57ddef329');
        $url = "https://polyas-verification.famoser.ch/verify?c=gmH_mAQix7kzTKPjXdyY19-WgP-9Sy0sSF4nHpboS5TPwvD21sw5D8HvYHFH7zSrI/yF4pnRGCM1d9nV&d=K1ZimkpfS2C83o5i5KfHdgGm9ZOzlm7QCbqQ6X09u1M1uTE8inq7tl2WCyyqcQaobSBezGvKM0nhLQNDPnAY2Pxharl9kkCX-Qik4kIJX8uG8BqBOhxeFDtexauLQ7FnZj1k81UZs96Cnw==&vid=1000&nonce=f74bee28a0e090c62947bfb63734310481049e21437c6df6e8f8f842267e3f44";
        $password = "743310";

        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $payload);
        /** @var array{'vid': string, 'd': string, 'nonce': string} $payload */

        $commit = ChallengeCommit::createWithRandom();
        $challengeCommitment = $commit->commit();
        $loginPayload = [
            "voterId" => $payload['vid'],
            "ballotReference" => Base64UrlEncoding::decode($payload['d']),
            "nonce" => $payload['nonce'],
            "password" => $password,
            'challengeCommitment' => $challengeCommitment,
        ];

        $loginResponse = $apiClient->postLogin($loginPayload);
        $this->assertNotNull($loginResponse);

        $challengePayload = ['challenge' => $commit->getEString(), 'challengeRandomCoin' => $commit->getRString()];
        $challengeResponse = $apiClient->postChallenge($challengePayload, $loginResponse['token']);
        $this->assertNotNull($challengeResponse);
    }
}
