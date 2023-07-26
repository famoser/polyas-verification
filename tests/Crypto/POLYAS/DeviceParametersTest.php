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
    public function testFingerprint(): void
    {
        $deviceParametersPayload = $this->getExamplePayload();
        $expectedFingerprint = $this->getDeviceParametersFingerprint();

        $fingerprint = DeviceParameters::createJsonFingerprint($deviceParametersPayload['secondDeviceParametersJson']);
        $this->assertEquals($expectedFingerprint, $fingerprint);

        // sanity check test
        $deviceParametersFromPayload = json_decode($deviceParametersPayload['secondDeviceParametersJson'], true);
        $deviceParameters = $this->getDeviceParameters();
        $this->assertEquals($deviceParametersFromPayload, $deviceParameters);

        // sanity check json serialization
        $expectedJson = $deviceParametersPayload['secondDeviceParametersJson'];
        $actualJson = json_encode($this->getDeviceParameters());
        $this->assertNotEquals($expectedJson, $actualJson);
        $this->assertEquals($expectedJson, str_replace('"value":[]', '"value":{}', $actualJson));
    }

    /**
     * @return array{
     *     'publicKey': string,
     *     'verificationKey': string,
     *     'ballots': mixed
     * }
     */
    private function getDeviceParameters(): array
    {
        $ballotEntryJson = file_get_contents(__DIR__.'/resources/deviceParameters/deviceParameters.json');

        return json_decode($ballotEntryJson, true); // @phpstan-ignore-line
    }

    /**
     * @return array{
     *     'secondDeviceParametersJson': string,
     * }
     */
    private function getExamplePayload(): array
    {
        $ballotEntryJson = file_get_contents(__DIR__.'/resources/deviceParameters/examplePayload.json');

        return json_decode($ballotEntryJson, true); // @phpstan-ignore-line
    }

    private function getDeviceParametersFingerprint(): string
    {
        /** @var string $fileContent */
        $fileContent = file_get_contents(__DIR__.'/resources/deviceParameters/deviceParameters.json.fingerprint');

        return trim($fileContent);
    }
}
