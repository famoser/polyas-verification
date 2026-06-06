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
use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use Mdanter\Ecc\Primitives\PointInterface;
use PHPUnit\Framework\TestCase;

class BallotDecodeTest extends TestCase
{
    public function testBallotDigestDigestedBytes(): void
    {
        $ballotDecode = $this->getBallotDecode();
        $expectedPlaintextHex = '00000001';

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
        $payload = Ballot0::getLoginResponseInitialMessage();
        $ballotDecode = $this->getBallotDecode();
        $expectedGroupElement = SECP256K1\Encoder::parseCompressedPoint('020007d00000005000000000000000000000000000000000000000000000000003');

        $ciphertexts = $payload['ballot']['encryptedChoice']['ciphertexts'];
        $decodeRandomCoins = $ballotDecode->getDecodeRandomCoins();
        $groupElement = $ballotDecode->getGroupElement($ciphertexts[0]['y'], $payload['factorY'][0], $decodeRandomCoins[0]);

        $this->assertTrue($expectedGroupElement->equals($groupElement));
    }

    private function getBallotDecode(): BallotDecode
    {
        $payload = Ballot0::getLoginResponseInitialMessage();
        $deviceParameters = Ballot0::getDeviceParameters();
        $randomCoinSeed = Ballot0::getQRCodeDecrypted()['randomCoinSeed'];

        return new BallotDecode($payload, $deviceParameters->getPublicKey(), $randomCoinSeed);
    }
}
