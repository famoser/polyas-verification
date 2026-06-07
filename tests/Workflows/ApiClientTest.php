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
            "voterId" => "voter7",
            "ballotReference" => "/EhRgDjIA+scXsSyfSXvqPCHsvbf/UozQicLbNd4bjkps8aP4ZXdo3R+KuvYX/ZM8NeAJcGrZbeb3wm8fgnby1gQJGqJwMY+eN6qXN83b0i5pNaej1WrMglE4KIXpDc8Bn00stxsvy0qlw==",
            "nonce" => "e552502592f5bec54e4750c769ae9a3ec913c69a7cd828ce0226201476a2f833",
            "password" => "711852",
            'challengeCommitment' => $challengeCommitment,
        ];
        $loginResponse = $apiClient->postLogin($loginPayload);
        $this->assertNotNull($loginResponse);

        $challengePayload = ['challenge' => $commit->getEString(), 'challengeRandomCoin' => $commit->getRString()];
        $challengeResponse = $apiClient->postChallenge($challengePayload, $loginResponse['value']['token']);
        $this->assertNotNull($challengeResponse);
    }
}
