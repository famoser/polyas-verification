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

use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Crypto\POLYAS\NumbersFromSeedInRange;
use Famoser\PolyasVerification\Crypto\POLYAS\ZKPProofValidation;
use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use Mdanter\Ecc\EccFactory;
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

    public function testCheckSamePlaintext(): void
    {
        $this->markTestIncompleteNS('One of the two points does not pass.');

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

        $randomCoinSeed = $this->getRandomCoinSeed();
        $order = EccFactory::getSecgCurves()->generator256k1()->getOrder();
        $randomCoinGenerator = new NumbersFromSeedInRange(count($ciphertexts), $randomCoinSeed, $order);
        $randomCoins = $randomCoinGenerator->numbers();
        $this->assertEquals('115383914388283582501768653457363159558776433376562817712059811925202949510311', gmp_strval($randomCoins[0]));

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
        return hex2bin('1e89b5f95deae82f6f823b52709117405f057783eda018d72cbd83141d394fbd');
    }
}
