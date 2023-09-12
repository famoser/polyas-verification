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

        $apiClient = new ApiClient('https://elections-k8s-dev.polyas.com/15d8e65f-1ccc-41a3-8fac-03361bc21979/');

        $commit = ChallengeCommit::createWithRandom();
        $challengeCommitment = $commit->commit();
        $loginPayload = [
            'voterId' => '110',
            'nonce' => '20e75f0b030268cefe5e23579ae3ac7152b9badc55099cba3dcb1e24ce900743',
            'password' => '265305',
            'challengeCommitment' => $challengeCommitment,
        ];
        $loginResponse = $apiClient->postLogin($loginPayload);
        $this->assertNotNull($loginResponse);

        $challengePayload = ['challenge' => $commit->getEString(), 'challengeRandomCoin' => $commit->getRString()];
        $challengeResponse = $apiClient->postChallenge($challengePayload, $loginResponse['token']);
        $this->assertNotNull($challengeResponse);
    }
}
