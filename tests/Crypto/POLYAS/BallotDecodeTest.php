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
use Famoser\PolyasVerification\Crypto\POLYAS\PlaintextEncoder;
use Famoser\PolyasVerification\Crypto\SECP256K1;
use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\PointInterface;
use PHPUnit\Framework\TestCase;

class BallotDecodeTest extends TestCase
{
    use IncompleteTestTrait;

    public function testBallotDigestDigestedBytes(): void
    {
        $this->markTestIncompleteNS('Math not implemented yet to derive group elements');

        $ballotDecode = $this->getBallotDecode();
        $expectedPlaintextHex = $this->getExpectedPlaintextHex();

        $message = $ballotDecode->decode();
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
        $this->markTestIncompleteNS('Math not implemented yet to derive group elements');

        $payload = $this->getTraceSecondDeviceInitialMsg();
        $ballotDecode = $this->getBallotDecode();
        $expectedGroupElement = $this->getExpectedGroupElement();

        $ciphertexts = $payload['ballot']['encryptedChoice']['ciphertexts'];
        $decodeRandomCoins = $ballotDecode->getDecodeRandomCoins();
        $groupElement = $ballotDecode->getGroupElement($ciphertexts[0]['y'], $payload['factorY'][0], $decodeRandomCoins[0]);

        $this->assertTrue($expectedGroupElement->equals($groupElement));
    }

    public function testGetGroupElementMath(): void
    {
        $this->markTestIncompleteNS('Group element does not match (but should)');

        $payload = $this->getTraceSecondDeviceInitialMsg();
        $publicKey = $this->getDeviceParameters()->getPublicKey();
        $expectedGroupElement = $this->getExpectedGroupElement();

        $decodeRandomCoins = $this->getBallotDecode()->getDecodeRandomCoins();

        $h = SECP256K1\Encoder::parseCompressedPoint($publicKey);

        $wPoint = SECP256K1\Encoder::parseCompressedPoint($payload['ballot']['encryptedChoice']['ciphertexts'][0]['y']);
        $YPoint = SECP256K1\Encoder::parseCompressedPoint($payload['factorY'][0]);

        $point1 = $wPoint->add($YPoint);
        $hPowerR = $h->mul($decodeRandomCoins[0]);

        $this->assertTrue($expectedGroupElement->add($hPowerR)->equals($point1));
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
        $expectedPlaintextHex = $this->getExpectedPlaintextHex();

        $order = EccFactory::getSecgCurves()->generator256k1()->getOrder();
        /** @var string $expectedPlaintext */
        $expectedPlaintext = hex2bin($expectedPlaintextHex);
        $expectedDecodedGroupElements = PlaintextEncoder::encodeMultiPlaintext($order, $expectedPlaintext);

        return PlaintextEncoder::encode($expectedDecodedGroupElements[0]);
    }
}
