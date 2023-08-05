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
use Famoser\PolyasVerification\Crypto\SECP256K1;
use Mdanter\Ecc\Primitives\PointInterface;
use PHPUnit\Framework\TestCase;

class BallotDecodeTest extends TestCase
{
    public function testBallotDigestDigestedBytes(): void
    {
        $ballotDecode = $this->getBallotDecode();
        $expectedPlaintextHex = $this->getExpectedPlaintextHex();

        $message = $ballotDecode->decode();
        $this->assertNotNull($message);
        $this->assertEquals($expectedPlaintextHex, bin2hex($message));
    }

    public function testCheckBallotDigestDigestedBytes(): void
    {
        $ballotDecode = $this->getBallotDecode();

        $randomCoins = $ballotDecode->getDecodeRandomCoins();

        $this->assertCount(1, $randomCoins);
        $this->assertEquals('115383914388283582501768653457363159558776433376562817712059811925202949510311', gmp_strval($randomCoins[0]));
    }

    public function testGetGroupElement(): void
    {
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $ballotDecode = $this->getBallotDecode();
        $expectedGroupElement = $this->getExpectedGroupElement();

        $ciphertexts = $payload['ballot']['encryptedChoice']['ciphertexts'];
        $decodeRandomCoins = $ballotDecode->getDecodeRandomCoins();
        $groupElement = $ballotDecode->getGroupElement($ciphertexts[0]['y'], $payload['factorY'][0], $decodeRandomCoins[0]);

        $this->assertTrue($expectedGroupElement->equals($groupElement));
    }

    private function getBallotDecode(): BallotDecode
    {
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $deviceParameters = $this->getDeviceParameters();
        $randomCoinSeed = $this->getRandomCoinSeed();

        return new BallotDecode($payload, $deviceParameters->getPublicKey(), $randomCoinSeed);
    }

    /**
     * @return array{
     *     'secondDeviceParametersJson': string,
     *     'factorY': string[],
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'y': string}}}
     *      }
     *     }
     */
    private function getTraceSecondDeviceInitialMsg(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/2_LoginResponse_initialMessage.json');

        return json_decode($json, true);
    }

    private function getDeviceParameters(): DeviceParameters
    {
        $deviceParametersPayload = $this->getTraceSecondDeviceInitialMsg();
        $deviceParametersJson = $deviceParametersPayload['secondDeviceParametersJson'];

        return new DeviceParameters($deviceParametersJson);
    }

    private function getRandomCoinSeed(): string
    {
        /** @var string $randomCoinSeed */
        $randomCoinSeed = hex2bin('1e89b5f95deae82f6f823b52709117405f057783eda018d72cbd83141d394fbd');

        return $randomCoinSeed;
    }

    public function getExpectedPlaintextHex(): string
    {
        return '00000001';
    }

    public function getExpectedGroupElement(): PointInterface
    {
        return SECP256K1\Encoder::parseCompressedPoint('020007d00000005000000000000000000000000000000000000000000000000003');
    }
}
