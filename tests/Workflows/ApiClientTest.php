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

        $apiClient = new ApiClient('https://election.polyas.com/97a5d0b7-e8a2-4832-97fe-71bc0e8d7786');
        $url = "https://polyas-verification.famoser.ch/verify?c=TB0gDjmdB9qzObC5bQvbnQfLFPcXpLM1eqkQKYTvDS3RafPiLr0mJe1cWbF_SL9-xmJ2mWvqB1VJs1Qa&d=0S2X4aP4YlOS0P2Pe67OsELiWolf083-QCwGk3U7L7lt1VHQpJx6Ji72PMEBNWVttKoc5wQnlrlNGNrQu8gJEgkLezx4QGAE0Hzbkdc_tPse5z0T7pNf-7B35lDf/Ptuw6BMGsPKbZ/L&vid=5001&nonce=7c3a102ebc45152919bfe94d221b7a22f440a6a28ac4a9159217331281a0426d";
        $password = "746286";

        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $payload);
        /** @var array{'vid': string, 'd': string, 'nonce': string} $payload */

        $commit = ChallengeCommit::createWithRandom();
        $challengeCommitment = $commit->commit();
        $loginPayload = [
            "voterId" => $payload['vid'],
            "ballotReference" => $payload['d'],
            "nonce" => $payload['nonce'],
            "password" => $password,
            'challengeCommitment' => $challengeCommitment,
        ];

        $loginResponse = $apiClient->postLogin($loginPayload);
        $this->assertNotNull($loginResponse);

        $challengePayload = ['challenge' => $commit->getEString(), 'challengeRandomCoin' => $commit->getRString()];
        $challengeResponse = $apiClient->postChallenge($challengePayload, $loginResponse['value']['token']);
        $this->assertNotNull($challengeResponse);
    }
}
