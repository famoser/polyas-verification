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
use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use Famoser\PolyasVerification\Workflow\ApiClient;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    use IncompleteTestTrait;

    public function testReceiptVerify(): void
    {
        $this->markTestIncompleteNS('Requires live server.');

        $apiClient = new ApiClient('https://election.polyas.com/a02435b3-3d7e-4a6c-ab9b-0b3dab1103ed/');
        $commit = ChallengeCommit::createWithRandom();
        $challengeCommitment = $commit->commit();
        $loginPayload = [
            'voterId' => 'voter40',
            'nonce' => 'a9ce03337895a1482c08361e8435c34ffa07857fad0c56efcd6807a28fc0f26a',
            'password' => '299406',
            'challengeCommitment' => $challengeCommitment,
        ];
        $loginResponse = $apiClient->postLogin($loginPayload);
        $this->assertNotNull($loginResponse);

        $challengePayload = ['challenge' => $commit->getEString(), 'challengeRandomCoin' => $commit->getRString()];
        $challengeResponse = $apiClient->postChallenge($challengePayload, $loginResponse['token']);
        $this->assertNotNull($challengeResponse);
    }
}
