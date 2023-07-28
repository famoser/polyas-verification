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
use PHPUnit\Framework\TestCase;

class DeviceParametersTest extends TestCase
{
    public function testCompareFingerprint(): void
    {
        $deviceParameters = $this->getDeviceParameters();
        $traceSecondDeviceInitialMsg = $this->getTraceSecondDeviceInitialMsg();

        $this->assertTrue($deviceParameters->compareDeviceParameters($traceSecondDeviceInitialMsg['secondDeviceParametersJson']));
    }

    public function testFingerprint(): void
    {
        $deviceParameters = $this->getDeviceParameters();
        $expectedFingerprint = $this->getDeviceParametersFingerprint();

        $this->assertEquals($expectedFingerprint, $deviceParameters->createFingerprint());
    }

    public function testJsonSerialization(): void
    {
        $traceSecondDeviceInitialMsg = $this->getTraceSecondDeviceInitialMsg();

        // sanity check json serialization
        $expectedJson = $traceSecondDeviceInitialMsg['secondDeviceParametersJson'];
        $object = json_decode($expectedJson, true);
        $actualJson = json_encode($object);
        $this->assertNotEquals($expectedJson, $actualJson);

        $correctedJson = str_replace('"value":[]', '"value":{}', $actualJson); // @phpstan-ignore-line
        $this->assertEquals($expectedJson, $correctedJson);
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
     * }
     */
    private function getTraceSecondDeviceInitialMsg(): array
    {
        $ballotDigestJson = file_get_contents(__DIR__.'/resources/ballot1/trace/2_LoginResponse_initialMessage.json');

        return json_decode($ballotDigestJson, true); // @phpstan-ignore-line
    }

    private function getDeviceParametersFingerprint(): string
    {
        /** @var string $fileContent */
        $fileContent = file_get_contents(__DIR__.'/resources/ballot1/deviceParameters/deviceParameters.json.fingerprint');

        return trim($fileContent);
    }
}
