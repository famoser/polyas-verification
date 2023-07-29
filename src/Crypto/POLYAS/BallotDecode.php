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
    public function __construct(private array $payload, private string $challenge, private array $zPayload, private string $publicKey, private string $randomCoinSeed)
    {
    }

    public function decode(): bool
    {
        $ciphertextCount = count($this->payload['ballot']['encryptedChoice']['ciphertexts']);
        $order = EccFactory::getSecgCurves()->generator256k1()->getOrder();
        $randomCoinGenerator = new NumbersFromSeedInRange($ciphertextCount, $this->randomCoinSeed, $order);
        $randomCoins = $randomCoinGenerator->numbers();
        for ($i = 0; $i < $ciphertextCount; ++$i) {
            $groupElement = $this->getGroupElement($this->payload['ballot']['encryptedChoice']['ciphertexts'][$i]['y'], $this->payload['factorY'][$i], $randomCoins[$i]);
            $decodedGroupElement = PlaintextEncoder::decode($groupElement);
        }

        return true;
    }

    public function getGroupElement(string $w, string $Y, \GMP $r)
    {
        $g = EccFactory::getSecgCurves()->generator256k1();
        $h = SECP256K1\Encoder::parseCompressedPoint($this->publicKey);

        $wPoint = SECP256K1\Encoder::parseCompressedPoint($w);
        $YPoint = SECP256K1\Encoder::parseCompressedPoint($Y);

        $point1 = $wPoint->add($YPoint);
        $hPowerR = $h->mul($r);
        // todo calculate inverse of $r

        return $point1->add($hPowerR);
    }
}
