<?php

declare(strict_types=1);

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\POLYAS\Utils\PlaintextEncodingException;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Exception\PointRecoveryException;
use Mdanter\Ecc\Primitives\PointInterface;

class PlaintextEncoder
{
    private const MAX_COUNTER = 80;

    public static function encode(\GMP $value): PointInterface
    {
        $curve = EccFactory::getSecgCurves()->curve256k1();
        $order = EccFactory::getSecgCurves()->generator256k1()->getOrder();

        $currentValue = $value;
        $counter = 1;

        do {
            try {
                $xPoint = gmp_mod(gmp_add($currentValue, 1), $order);
                $yPoint = $curve->recoverYfromX(false, $xPoint);

                return $curve->getPoint($xPoint, $yPoint);
            } catch (PointRecoveryException) {
                // ok. no need to handle
            }

            $currentValue = gmp_add($currentValue, $value);
        } while ($counter++ < self::MAX_COUNTER); // loop iterates INCLUDING max counter

        throw new PlaintextEncodingException('Value cannot be encoded');
    }

    public static function decode(PointInterface $point): \GMP
    {
        return gmp_div(gmp_sub($point->getX(), 1), self::MAX_COUNTER);
    }

    /**
     * @return \GMP[]
     */
    public static function encodeMultiPlaintext(\GMP $q, string $message): array
    {
        $blockSize = self::getBlockSize($q);

        $paddingSize = ($blockSize - ((strlen($message) + 2) % $blockSize)) % $blockSize;
        $zeroPadding = str_repeat("\0", $paddingSize);
        $paddingSizePrefix = pack('n', $paddingSize);
        $paddedMessage = $paddingSizePrefix.$message.$zeroPadding;

        /** @var \GMP[] $result */
        $result = [];
        for ($i = 0; $i < strlen($paddedMessage) / $blockSize; ++$i) {
            $result[] = gmp_import(substr($paddedMessage, $i * $blockSize, $blockSize));
        }

        return $result;
    }

    /**
     * @param \GMP[] $numbers
     *
     * @throws PlaintextEncodingException
     */
    public static function decodeMultiPlaintext(\GMP $q, array $numbers): string
    {
        $blockSize = self::getBlockSize($q);
        $messageWithPadding = '';
        foreach ($numbers as $number) {
            $stringNumber = gmp_export($number);
            $messageWithPadding .= str_pad($stringNumber, $blockSize, "\0", STR_PAD_LEFT);
        }

        $paddingSizeString = substr($messageWithPadding, 0, 2);
        $paddingSize = (int) unpack('n', $paddingSizeString)[1]; // @phpstan-ignore-line

        $expectedPadding = str_repeat("\0", $paddingSize);
        if (!str_ends_with($messageWithPadding, $expectedPadding)) {
            throw new PlaintextEncodingException('Invalid padding');
        }

        return substr($messageWithPadding, 2, -$paddingSize ?: null);
    }

    /**
     * Gets the byte block size allowed by the number q.
     * Works for all numbers not equal $q = e^x for some x. As $q is prime, this is given.
     */
    public static function getBlockSize(\GMP $q): int
    {
        $log2 = strlen(gmp_strval($q, 2)) - 1;

        return (int) floor($log2 / 8);
    }
}
