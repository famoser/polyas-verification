<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use PHPUnit\Framework\TestCase;

class ChallengeCommitTest extends TestCase
{
    public function testValidateFromSampleData(): void
    {
        $challengeCommitment = $this->getTraceLoginRequest()['challengeCommitment'];
        $challengeCommit = $this->getChallengeCommit();

        $commit = $challengeCommit->commit();

        $this->assertEquals($challengeCommitment, $commit);
    }

    public function testValidateWithFreshRandomness(): void
    {
        $challengeCommit = ChallengeCommit::createWithRandom();

        $commit = $challengeCommit->commit();

        $this->assertTrue($challengeCommit->verify($commit));
    }

    private function getChallengeCommit(): ChallengeCommit
    {
        $request = $this->getTraceChallengeRequest();

        $challenge = gmp_init($request['challenge'], 10);
        $challengeRandomCoin = gmp_init($request['challengeRandomCoin'], 10);

        return new ChallengeCommit($challenge, $challengeRandomCoin);
    }

    /**
     * @return array{
     *     'challengeCommitment': string,
     * }
     */
    private function getTraceLoginRequest(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/1_LoginRequest.json');

        return json_decode($json, true);
    }

    /**
     * @return array{
     *     'challenge': string,
     *     'challengeRandomCoin': string,
     * }
     */
    private function getTraceChallengeRequest(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/3_ChallengeRequest.json');

        return json_decode($json, true);
    }
}
