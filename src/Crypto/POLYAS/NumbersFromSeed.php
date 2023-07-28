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

readonly class NumbersFromSeed
{
    public function __construct(private string $seed, private int $bitLength, private int $iteration = 1)
    {
    }

    public function number(): \GMP
    {
        if (0 === $this->bitLength) {
            return gmp_init(0);
        }

        $keyDerivationKey = $this->seed.pack('N', $this->iteration);
        $byteLength = (int) ceil($this->bitLength / 8.0);
        $keyDerivation = new KeyDerivation($keyDerivationKey, $byteLength, 'generator', 'Polyas');

        $key = $keyDerivation->derive();
        $positiveKey = "\0".$key;

        // cut off top bits if necessary
        if ($this->bitLength % 8 > 0) {
            $firstByte = substr($positiveKey, 0, 2);
            $firstNumber = unpack('n', $firstByte)[1]; // @phpstan-ignore-line

            $keepBits = $this->bitLength % 8;
            $bitsToClear = $firstNumber >> $keepBits;
            $firstNumberWithClearedBits = $firstNumber ^ ($bitsToClear << $keepBits);

            $clearedFirstByte = pack('n', $firstNumberWithClearedBits);
            $positiveKey = $clearedFirstByte.substr($key, 2);
        }

        return gmp_import($positiveKey);
    }

    public function iterate(): self
    {
        return new self($this->seed, $this->bitLength, $this->iteration + 1);
    }
}
