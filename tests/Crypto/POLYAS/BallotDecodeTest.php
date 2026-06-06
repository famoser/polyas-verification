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
        $expectedPlaintextHex = '00000100';

        $message = $ballotDecode->decode();
        $this->assertNotNull($message);
        $this->assertEquals($expectedPlaintextHex, bin2hex($message));
    }

    public function testCheckDecodedRandomCoins(): void
    {
        $payload = Ballot0::getLoginResponseInitialMessage();
        $ciphertexts = $payload['ballot']['encryptedChoice']['ciphertexts'];

        $ballotDecode = $this->getBallotDecode();
        $randomCoins = $ballotDecode->getDecodeRandomCoins();
        $this->assertEquals(count($ciphertexts), count($randomCoins));
        $this->assertEquals('40237237455298050319120936869549056473558681858388723323151943196272052465320', gmp_strval($randomCoins[0]));
    }

    public function testGetGroupElement(): void
    {
        $payload = Ballot0::getLoginResponseInitialMessage();
        $ballotDecode = $this->getBallotDecode();

        $ciphertexts = $payload['ballot']['encryptedChoice']['ciphertexts'];
        $decodeRandomCoins = $ballotDecode->getDecodeRandomCoins();
        $groupElement = $ballotDecode->getGroupElement($ciphertexts[0]['y'], $payload['factorY'][0], $decodeRandomCoins[0]);

        $this->assertEquals('030007d00000500000000000000000000000000000000000000000000000000001', SECP256K1\Encoder::compressPoint($groupElement));
    }

    private function getBallotDecode(): BallotDecode
    {
        $payload = Ballot0::getLoginResponseInitialMessage();
        $deviceParameters = Ballot0::getDeviceParameters();
        $randomCoinSeed = Ballot0::getRandomCoinSeed();

        return new BallotDecode($payload, $deviceParameters->getPublicKey(), $randomCoinSeed);
    }
}
