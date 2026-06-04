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

readonly class UniformHash
{
    public function __construct(private \GMP $q, private string $input)
    {
    }

    public function hash(): \GMP
    {
        $h = hash('sha512', $this->input, true);
        $numbersFromSeed = new NumbersFromSeedInRange(1, $h, $this->q);

        return $numbersFromSeed->numbers()[0];
    }
}
