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

use Famoser\PolyasVerification\Crypto\POLYAS\PlaintextEncoder;
use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\PointInterface;
use PHPUnit\Framework\TestCase;

class PlaintextEncoderTest extends TestCase
{
    use IncompleteTestTrait;

    public function testEncode(): void
    {
        $this->markTestIncompleteNS('Encoding probably wrong specified.');

        $value = $this->getValue();
        $point = $this->getPoint();

        $encoded = PlaintextEncoder::encode($value);

        $this->assertTrue($point->equals($encoded));
    }

    public function testEncodeMultiPlaintext(): void
    {
        $message = 'hi mom';
        $q = gmp_init(257);

        $encodedNumbers = PlaintextEncoder::encodeMultiPlaintext($q, $message);
        $decodedMessage = PlaintextEncoder::decodeMultiPlaintext($q, $encodedNumbers);

        $this->assertCount(strlen($message) + 2, $encodedNumbers);
        $this->assertEquals($message, $decodedMessage);
    }

    public function testEncodeMultiPlaintextMultiBlock(): void
    {
        $message = 'hi mom!';
        $q = gmp_init(65537);

        $encodedNumbers = PlaintextEncoder::encodeMultiPlaintext($q, $message);
        $decodedMessage = PlaintextEncoder::decodeMultiPlaintext($q, $encodedNumbers);

        $bytesRequired = strlen($message) + 2;
        $this->assertCount(ceil($bytesRequired / 2.0), $encodedNumbers);
        $this->assertEquals($message, $decodedMessage);
    }

    public function testEncodeMultiPlaintextRealisticBlockSize(): void
    {
        $message = 'hi mom! how is it going? this is a really long text to surpass the block limit.';
        $q = EccFactory::getSecgCurves()->generator256k1()->getOrder();

        $encodedNumbers = PlaintextEncoder::encodeMultiPlaintext($q, $message);
        $decodedMessage = PlaintextEncoder::decodeMultiPlaintext($q, $encodedNumbers);

        $this->assertCount(3, $encodedNumbers);
        $this->assertEquals($message, $decodedMessage);
    }

    public function testEncodingReversal(): void
    {
        $value = $this->getValue();
        $expectedXPoint = gmp_init('7fffffffffffffffffffffffffffffffffffffffffffffffffffffff7ffffe21', 16);

        for ($i = 1; $i <= 80; ++$i) {
            $almostExpectedX = gmp_add(gmp_mul($value, $i), 1);

            if (0 === gmp_cmp($expectedXPoint, $almostExpectedX)) {
                $this->assertEquals(80, $i);
            }
        }
    }

    public function testDecode(): void
    {
        $point = $this->getPoint();
        $value = $this->getValue();

        $decoded = PlaintextEncoder::decode($point);

        $this->assertTrue(0 === gmp_cmp($value, $decoded));
    }

    public function getPoint(): PointInterface
    {
        $expectedX = gmp_init('7fffffffffffffffffffffffffffffffffffffffffffffffffffffff7ffffe21', 16);
        $expectedY = gmp_init('2af4d53f09f4d4ede3caf3f0e06ccfc0f55289d83fed859ca504d6033bec629b', 16);
        $curve = EccFactory::getSecgCurves()->curve256k1();

        return $curve->getPoint($expectedX, $expectedY);
    }

    public function getValue(): \GMP
    {
        return gmp_init('723700557733226221397318656304299424082937404160253525246609900049430216698', 10);
    }
}
