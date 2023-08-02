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
use Famoser\PolyasVerification\Crypto\SECP256K1;
use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use PHPUnit\Framework\TestCase;

class ZKPProofValidationTest extends TestCase
{
    use IncompleteTestTrait;

    public function testValidate(): void
    {
        $this->markTestIncompleteNS('One of the two points does not pass.');

        $ZKPProofValidation = $this->getZKPProofValidation();

        $this->assertTrue($ZKPProofValidation->validate());
    }

    public function testCheckExpectedCiphertextLengths(): void
    {
        $ZKPProofValidation = $this->getZKPProofValidation();
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $ciphertextCount = count($payload['ballot']['encryptedChoice']['ciphertexts']);

        $this->assertTrue($ZKPProofValidation->checkExpectedCiphertextLengths($ciphertextCount));
    }

    public function testCheckExponentiation(): void
    {
        $publicKey = SECP256K1\Encoder::parseCompressedPoint('030588c6c80497da9e50bf56a4853c9fd3dd945a5e2ed741ccf783c5538611da26');
        $k = gmp_init('3633826251616834446657553661530373736489206587264246793596555854504147120873052400272122845815239659486740186516083053240689380948861192914781931033170662');
        $phpResult = SECP256K1\Encoder::parseCompressedPoint('02328037239284409b2e2a554c41c1cbfb02155111175fcefd4c6a3ca062377c77');
        $javaScriptResult = SECP256K1\Encoder::parseCompressedPoint('03705a21c0722b5ce27e92d6ba9ece486242153afa57865fda4249c997b7b87ddc');

        $actualResult = $publicKey->mul($k);
        $this->assertTrue($actualResult->equals($phpResult));
        $this->assertFalse($actualResult->equals($javaScriptResult));
        $this->assertFalse($phpResult->equals($javaScriptResult));
    }

    public function testCheckSamePlaintext(): void
    {
        $ZKPProofValidation = $this->getZKPProofValidation();
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $zResponse = $this->getTraceChallengeResponseValue();

        $checkReEncryption = $ZKPProofValidation->checkSamePlaintext($payload['factorA'][0], $payload['factorB'][0], $payload['factorX'][0], $payload['factorY'][0], $zResponse['z'][0]);
        $this->assertTrue($checkReEncryption);
    }

    public function testCheckReEncryption(): void
    {
        $ZKPProofValidation = $this->getZKPProofValidation();
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $ciphertexts = $payload['ballot']['encryptedChoice']['ciphertexts'];
        $factorX = $payload['factorX'];
        $ballotDecode = $this->getBallotDecode();
        $randomCoins = $ballotDecode->getDecodeRandomCoins();

        $checkReEncryption = $ZKPProofValidation->checkReEncryption($ciphertexts[0]['x'], $factorX[0], $randomCoins[0]);
        $this->assertTrue($checkReEncryption);
    }

    private function getZKPProofValidation(): ZKPProofValidation
    {
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $request = $this->getTraceChallengeRequest();
        $response = $this->getTraceChallengeResponseValue();
        $deviceParameters = $this->getDeviceParameters();
        $randomCoinSeed = $this->getRandomCoinSeed();

        return new ZKPProofValidation($payload, $request['challenge'], $response['z'], $deviceParameters->getPublicKey(), $randomCoinSeed);
    }

    private function getBallotDecode(): BallotDecode
    {
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $deviceParameters = $this->getDeviceParameters();
        $randomCoinSeed = $this->getRandomCoinSeed();

        return new BallotDecode($payload, $deviceParameters->getPublicKey(), $randomCoinSeed);
    }

    private function getDeviceParameters(): DeviceParameters
    {
        $deviceParametersPayload = $this->getTraceSecondDeviceInitialMsg();
        $deviceParametersJson = $deviceParametersPayload['secondDeviceParametersJson'];

        return new DeviceParameters($deviceParametersJson);
    }

    /**
     * @return array{
     *     'secondDeviceParametersJson': string,
     *     'factorA': string[],
     *     'factorB': string[],
     *     'factorX': string[],
     *     'factorY': string[],
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *      }
     *     }
     */
    private function getTraceSecondDeviceInitialMsg(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/2_LoginResponse_initialMessage.json');

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

    /**
     * @return array{
     *     'z': string[],
     * }
     */
    private function getTraceChallengeResponseValue(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/4_ChallengeResponse_value.json');

        return json_decode($json, true);
    }

    private function getRandomCoinSeed(): string
    {
        /** @var string $randomCoinSeed */
        $randomCoinSeed = hex2bin('1e89b5f95deae82f6f823b52709117405f057783eda018d72cbd83141d394fbd');

        return $randomCoinSeed;
    }
}
