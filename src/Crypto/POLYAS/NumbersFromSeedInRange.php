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

class NumbersFromSeedInRange
{
    private int $iterationsSkipped = 0;

    public function __construct(private readonly string $seed, private readonly \GMP $maxNumber, private readonly int $startIteration = 1)
    {
    }

    public function number(): \GMP
    {
        $bitLength = strlen(gmp_strval($this->maxNumber, 2));
        $numbersFromSeed = new NumbersFromSeed($this->seed, $bitLength, $this->startIteration);

        while (true) {
            $number = $numbersFromSeed->number();
            if ($number < $this->maxNumber) {
                return $number;
            }

            $numbersFromSeed = $numbersFromSeed->iterate();
            ++$this->iterationsSkipped;
        }
    }

    public function iterate(): self
    {
        return new self($this->seed, $this->maxNumber, $this->startIteration + $this->iterationsSkipped + 1);
    }
}
