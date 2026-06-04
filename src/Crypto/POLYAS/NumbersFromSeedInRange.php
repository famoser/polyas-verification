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
    public function __construct(private string $seed, private \GMP $maxNumber, private int $startIteration = 1)
    {
    }

    /**
     * @return \Iterator<\GMP>
     */
    public function getIterator(): \Iterator
    {
        $bitLength = strlen(gmp_strval($this->maxNumber, 2));
        $numbersFromSeed = new NumberFromSeed($this->seed, $bitLength, $this->startIteration);

        while (true) {
            $number = $numbersFromSeed->number();
            if ($number < $this->maxNumber) {
                yield $number;
            }

            $numbersFromSeed = $numbersFromSeed->iterate();
        }
    }

    /**
     * @return \GMP[]
     */
    public function get(int $size): array
    {
        $numbers = [];
        foreach ($this->getIterator() as $number) {
            $numbers[] = $number;

            if (count($numbers) === $size) {
                break;
            }
        }

        return $numbers;
    }
}
