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
use Famoser\PolyasVerification\Crypto\SECP256K1\Encoder;
use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use PHPUnit\Framework\TestCase;

class ChallengeCommitTest extends TestCase
{
    public function testValidateFromSampleData(): void
    {
        $request = Ballot0::getChallengeRequest();
        $challenge = gmp_init($request['challenge'], 10);
        $challengeRandomCoin = gmp_init($request['challengeRandomCoin'], 10);

        $challengeCommit = new ChallengeCommit($challenge, $challengeRandomCoin);
        $commit = $challengeCommit->commit();

        $expectedCommit = Ballot0::getLoginRequest()['challengeCommitment'];
        $this->assertEquals($expectedCommit, $commit);
    }

    public function testValidateWithFreshRandomness(): void
    {
        $challengeCommit = ChallengeCommit::createWithRandom();

        $commit = $challengeCommit->commit();

        $this->assertTrue($challengeCommit->verify($commit));
    }
}
