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
    public function __construct(private array $payload, private array $zPayload, private string $challenge, private string $publicKey)
    {
    }

    public function validate(): bool
    {
        if (!$this->checkExpectedCiphertextLengths()) {
            return false;
        }

        for ($i = 0; $i < count($this->payload['ballot']['encryptedChoice']); ++$i) {
            if (!$this->checkYKPValid($this->payload['factorA'][$i], $this->payload['factorB'][$i], $this->payload['factorX'][$i], $this->payload['factorY'][$i], $this->zPayload[$i])) {
                return false;
            }
        }

        return true;
    }

    private function checkExpectedCiphertextLengths(): bool
    {
        $expectedLength = count($this->payload['ballot']['encryptedChoice']);
        $factorALength = count($this->payload['factorA']);
        $factorBLength = count($this->payload['factorB']);
        $factorXLength = count($this->payload['factorX']);
        $factorYLength = count($this->payload['factorY']);
        $zLength = count($this->zPayload);

        return $expectedLength === $factorALength && $expectedLength === $factorBLength && $expectedLength === $factorXLength && $expectedLength === $factorYLength && $expectedLength === $zLength;
    }

    private function checkYKPValid(string $A, string $B, string $X, string $Y, string $zString): bool
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
}
