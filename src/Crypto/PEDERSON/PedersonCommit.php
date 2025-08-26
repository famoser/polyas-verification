<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\PEDERSON;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;

readonly class PedersonCommit
{
    public function __construct(private Point $generatorG, private Point $generatorH)
    {
    }

    public function commit(MathInterface $math, \GMP $randomR, \GMP $messageM): Point
    {
        $left = $math->mul($this->generatorG, $randomR);
        $right = $math->mul($this->generatorH, $messageM);

        return $math->add($left, $right);
    }

    public function verify(MathInterface $math, Point $commitment, \GMP $randomR, \GMP $messageM): bool
    {
        $expectedCommit = $this->commit($math, $randomR, $messageM);

        return $expectedCommit->equals($commitment);
    }
}
