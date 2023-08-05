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
use Famoser\PolyasVerification\Workflow\Verification;
use PHPUnit\Framework\TestCase;

class VerificationTest extends TestCase
{
    use IncompleteTestTrait;

    public function testReceiptVerify(): void
    {
        $this->markTestIncompleteNS('The zero-knowledge proof does not pass.');

        $input = json_decode(file_get_contents(__DIR__.'/resources/ballot0/0_QRcode.json'), true); // @phpstan-ignore-line
        $deviceParametersJson = file_get_contents(__DIR__.'/resources/ballot0/deviceParameters.json');

        $apiClient = \Mockery::mock(ApiClient::class);
        $loginRequest = json_decode(file_get_contents(__DIR__.'/resources/ballot0/1_LoginRequest.json'), true); // @phpstan-ignore-line
        $loginResponse = json_decode(file_get_contents(__DIR__.'/resources/ballot0/2_LoginResponse.json'), true); // @phpstan-ignore-line
        $apiClient->shouldReceive('postLogin')->with($loginRequest)->andReturn($loginResponse); // @phpstan-ignore-line

        $challengeRequest = json_decode(file_get_contents(__DIR__.'/resources/ballot0/3_ChallengeRequest.json'), true); // @phpstan-ignore-line
        $challengeResponse = json_decode(file_get_contents(__DIR__.'/resources/ballot0/4_ChallengeResponse.json'), true); // @phpstan-ignore-line
        $token = 'MDIwNWJmMmUxNDQ5NmY2OGMwZjg2ZjZiMzEzZjIxMGE5MzkzZWRiMDgzODIxZGNjNGY5OTE0Y2FiOWM1MWM5ZjJl.UjVXYXRxTlRzdk12QWRwOA==';
        $apiClient->shouldReceive('postChallenge')->with($challengeRequest, $token)->andReturn($challengeResponse); // @phpstan-ignore-line

        $challenge = gmp_init($challengeRequest['challenge'], 10);
        $challengeRandomCoin = gmp_init($challengeRequest['challengeRandomCoin'], 10);
        $commit = new ChallengeCommit($challenge, $challengeRandomCoin);

        $verification = new Verification($deviceParametersJson, $apiClient);  // @phpstan-ignore-line
        $validationResult = $verification->verify($input, $commit, $error);
        $this->assertNull($error);
        $this->assertEquals($validationResult, '00000001');
    }
}
