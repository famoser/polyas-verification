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

readonly class ZKPProofValidation
{
    /**
     * @param array{
     *     'factorA': string[],
     *     'factorB': string[],
     *     'factorX': string[],
     *     'factorY': string[],
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *      }
     *     } $payload
     * @param string[] $zPayload
     */
    public function __construct(private array $payload, private string $challenge, private array $zPayload, private string $publicKey, private string $randomCoinSeed)
    {
    }

    public function validate(): bool
    {
        $ciphertextCount = count($this->payload['ballot']['encryptedChoice']['ciphertexts']);
        if (!$this->checkExpectedCiphertextLengths($ciphertextCount)) {
            return false;
        }

        for ($i = 0; $i < $ciphertextCount; ++$i) {
            if (!$this->checkSamePlaintext($this->payload['factorA'][$i], $this->payload['factorB'][$i], $this->payload['factorX'][$i], $this->payload['factorY'][$i], $this->zPayload[$i])) {
                return false;
            }
        }

        $order = EccFactory::getSecgCurves()->generator256k1()->getOrder();
        $randomCoinGenerator = new NumbersFromSeedInRange($ciphertextCount, $this->randomCoinSeed, $order);
        $randomCoins = $randomCoinGenerator->numbers();
        for ($i = 0; $i < $ciphertextCount; ++$i) {
            if (!$this->checkReEncryption($this->payload['ballot']['encryptedChoice']['ciphertexts'][$i]['x'], $this->payload['factorX'][$i], $randomCoins[$i])) {
                return false;
            }
        }

        return true;
    }

    public function checkExpectedCiphertextLengths(int $ciphertextCount): bool
    {
        $factorALength = count($this->payload['factorA']);
        $factorBLength = count($this->payload['factorB']);
        $factorXLength = count($this->payload['factorX']);
        $factorYLength = count($this->payload['factorY']);
        $zLength = count($this->zPayload);

        return $ciphertextCount === $factorALength && $ciphertextCount === $factorBLength && $ciphertextCount === $factorXLength && $ciphertextCount === $factorYLength && $ciphertextCount === $zLength;
    }

    public function checkSamePlaintext(string $A, string $B, string $X, string $Y, string $zString): bool
    {
        $g = EccFactory::getSecgCurves()->generator256k1();
        $h = SECP256K1\Encoder::parseCompressedPoint($this->publicKey);

        $aPoint = SECP256K1\Encoder::parseCompressedPoint($A);
        $bPoint = SECP256K1\Encoder::parseCompressedPoint($B);
        $xPoint = SECP256K1\Encoder::parseCompressedPoint($X);
        $yPoint = SECP256K1\Encoder::parseCompressedPoint($Y);

        $z = gmp_init($zString, 10);
        $e = gmp_init($this->challenge, 10);

        $AXValid = $aPoint->add($xPoint->mul($e))->equals($g->mul($z));
        $BYValid = $bPoint->add($yPoint->mul($e))->equals($h->mul($z));

        return $AXValid && $BYValid;
    }

    public function checkReEncryption(string $u, string $X, \GMP $r): bool
    {
        $g = EccFactory::getSecgCurves()->generator256k1();

        $uPoint = SECP256K1\Encoder::parseCompressedPoint($u);
        $xPoint = SECP256K1\Encoder::parseCompressedPoint($X);

        $right = $g->mul($r);
        $left = $uPoint->add($xPoint);

        return $left->equals($right);
    }
}
