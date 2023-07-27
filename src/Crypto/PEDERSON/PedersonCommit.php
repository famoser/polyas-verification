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

use Mdanter\Ecc\Primitives\PointInterface;

readonly class PedersonCommit
{
    public function __construct(private PointInterface $generatorG, private PointInterface $generatorH)
    {
    }

    public function commit(\GMP $randomR, \GMP $messageM): PointInterface
    {
        $left = $this->generatorG->mul($randomR);
        $right = $this->generatorH->mul($messageM);

        return $left->add($right);
    }

    public function verify(PointInterface $commitment, \GMP $randomR, \GMP $messageM): bool
    {
        $expectedCommit = $this->commit($randomR, $messageM);

        return $expectedCommit->equals($commitment);
    }
}
