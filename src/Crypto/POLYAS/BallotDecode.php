<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\SECP256K1;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Primitives\PointInterface;

class BallotDecode
{
    /**
     * @param array{
     *     'factorY': string[],
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'y': string}}}
     *      }
     *     } $payload
     */
    public function __construct(private array $payload, private string $publicKey, private string $randomCoinSeed)
    {
    }

    public function decode(): string
    {
        $ciphertextCount = count($this->payload['ballot']['encryptedChoice']['ciphertexts']);
        $order = EccFactory::getSecgCurves()->generator256k1()->getOrder();
        $randomCoinGenerator = new NumbersFromSeedInRange($ciphertextCount, $this->randomCoinSeed, $order);
        $randomCoins = $randomCoinGenerator->numbers();

        /** @var \GMP[] $decodedGroupElements */
        $decodedGroupElements = [];
        for ($i = 0; $i < $ciphertextCount; ++$i) {
            $groupElement = $this->getGroupElement($this->payload['ballot']['encryptedChoice']['ciphertexts'][$i]['y'], $this->payload['factorY'][$i], $randomCoins[$i]);
            $decodedGroupElements[] = PlaintextEncoder::decode($groupElement);
        }

        return PlaintextEncoder::decodeMultiPlaintext($order, $decodedGroupElements);
    }

    public function getGroupElement(string $w, string $Y, \GMP $r): PointInterface
    {
        $g = EccFactory::getSecgCurves()->generator256k1();
        $h = SECP256K1\Encoder::parseCompressedPoint($this->publicKey);

        $wPoint = SECP256K1\Encoder::parseCompressedPoint($w);
        $YPoint = SECP256K1\Encoder::parseCompressedPoint($Y);

        $point1 = $wPoint->add($YPoint);
        $hPowerR = $h->mul($r);

        // TODO: Should be division
        return $point1->add($hPowerR);
    }

    /**
     * @return \GMP[]
     */
    public function getDecodeRandomCoins(): array
    {
        $ciphertextCount = count($this->payload['ballot']['encryptedChoice']['ciphertexts']);
        $order = EccFactory::getSecgCurves()->generator256k1()->getOrder();
        $randomCoinGenerator = new NumbersFromSeedInRange($ciphertextCount, $this->randomCoinSeed, $order);

        return $randomCoinGenerator->numbers();
    }
}
