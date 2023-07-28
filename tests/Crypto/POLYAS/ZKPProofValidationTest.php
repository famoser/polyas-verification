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
use Famoser\PolyasVerification\Crypto\POLYAS\ZKPProofValidation;
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

    private function getZKPProofValidation(): ZKPProofValidation
    {
        $deviceParameters = $this->getDeviceParameters();
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $request = $this->getTraceChallengeRequest();
        $response = $this->getTraceChallengeResponseValue();

        return new ZKPProofValidation($payload, $response['z'], $request['challenge'], $deviceParameters->getPublicKey());
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
}
