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

readonly class NumbersFromSeedInRange
{
    public function __construct(private int $size, private string $seed, private \GMP $maxNumber, private int $startIteration = 1)
    {
    }

    /**
     * @return \GMP[]
     */
    public function numbers(): array
    {
        $bitLength = strlen(gmp_strval($this->maxNumber, 2));
        $numbersFromSeed = new NumberFromSeed($this->seed, $bitLength, $this->startIteration);
        /** @var \GMP[] $result */
        $result = [];

        while (count($result) < $this->size) {
            $number = $numbersFromSeed->number();
            if ($number < $this->maxNumber) {
                $result[] = $number;
            }

            $numbersFromSeed = $numbersFromSeed->iterate();
        }

        return $result;
    }
}
