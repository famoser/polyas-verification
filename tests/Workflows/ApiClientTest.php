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

use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use Famoser\PolyasVerification\Workflow\ApiClient;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    public function testReceiptVerify(): void
    {
        $apiClient = new ApiClient('https://election.polyas.com/2dea9d6e-1615-4d33-beba-eccbfa653093/');

        http://localhost:4300/?c=&vid=&nonce=

        $commit = ChallengeCommit::createWithRandom();
        $challengeCommitment = $commit->commit();
        $loginPayload = [
            'voterId' => 'voter3',
            'nonce' => 'f299af96450db626754147aa132237bbf5603df2eea8215a0859288df8015c85',
            'password' => '295181',
            'challengeCommitment' => $challengeCommitment,
        ];
        $loginResponse = $apiClient->postLogin($loginPayload);
        $this->assertNotNull($loginResponse);

        $challengePayload = ['challenge' => $commit->getEString(), 'challengeRandomCoin' => $commit->getRString()];
        $challengeResponse = $apiClient->postChallenge($challengePayload, $loginResponse['token']);
        $this->assertNotNull($challengeResponse);
    }
}
