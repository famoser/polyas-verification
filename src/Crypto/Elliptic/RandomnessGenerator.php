<?php

namespace Famoser\PolyasVerification\Crypto\Elliptic;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Point;

readonly class RandomnessGenerator
{
    private \GMP $order;

    public function __construct(private MathInterface $math)
    {
        $this->order = gmp_mul($this->math->getCurve()->getN(), $this->math->getCurve()->getH());
    }

    public function point(): Point
    {
        $random = $this->scalar();

        return $this->math->mulG($random);
    }

    public function scalar(): \GMP
    {
        return gmp_random_range(0, $this->order);
    }
}
