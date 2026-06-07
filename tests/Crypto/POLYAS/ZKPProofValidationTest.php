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

use Famoser\PolyasVerification\Crypto\POLYAS\BallotDecode;
use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Crypto\POLYAS\ZKPProofValidation;
use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use PHPUnit\Framework\TestCase;

class ZKPProofValidationTest extends TestCase
{
    public function testValidate(): void
    {
        $ZKPProofValidation = $this->getZKPProofValidation();

        $this->assertTrue($ZKPProofValidation->validate());
    }

    public function testCheckExpectedCiphertextLengths(): void
    {
        $ZKPProofValidation = $this->getZKPProofValidation();
        $payload = Ballot0::getLoginResponseInitialMessage();
        $ciphertextCount = count($payload['ballot']['encryptedChoice']['ciphertexts']);

        $this->assertTrue($ZKPProofValidation->checkExpectedCiphertextLengths($ciphertextCount));
    }

    public function testCheckSamePlaintext(): void
    {
        $ZKPProofValidation = $this->getZKPProofValidation();
        $payload = Ballot0::getLoginResponseInitialMessage();
        $response = Ballot0::getChallengeResponse();

        $checkReEncryption = $ZKPProofValidation->checkSamePlaintext($payload['factorA'][0], $payload['factorB'][0], $payload['factorX'][0], $payload['factorY'][0], $response['z'][0]);
        $this->assertTrue($checkReEncryption);
    }

    private function getZKPProofValidation(): ZKPProofValidation
    {
        $payload = Ballot0::getLoginResponseInitialMessage();
        $request = Ballot0::getChallengeRequest();
        $response = Ballot0::getChallengeResponse();
        $deviceParameters = Ballot0::getDeviceParameters();
        $randomCoinSeed = Ballot0::getRandomCoinSeed();

        $challenge = gmp_init($request['challenge'], 10);

        return new ZKPProofValidation($payload, $challenge, $response['z'], $deviceParameters->getPublicKey(), $randomCoinSeed);
    }
}
