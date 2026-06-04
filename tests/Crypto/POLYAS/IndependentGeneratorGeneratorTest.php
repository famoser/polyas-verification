<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\POLYAS\IndependentGeneratorGenerator;
use Famoser\PolyasVerification\Crypto\POLYAS\NumberFromSeed;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IndependentGeneratorGeneratorTest extends TestCase
{
    /**
     * @return array<array<string|int|array<string>>>
     */
    public static function generatorsUsingSeedAsSeed(): array
    {
        return [
            [1, ['879f580dfe31c74dc2b4289f1988e581c76e625761a863971c808e90ab6fd3c7', '4c59d9061d35678d06c04fe9f61dd47d7ee9e35b9847e5f3f9ed532c509afc0f']],
            [2, ['b6413eb866319a631509ad0e637ec260507383d7495ef66858f9a6a4bb8efac7', 'adb88f8cdd62aad64e2518d383b4e6aa2013910964b6423c17c0100f96118ae1']],
            [3, ['1845cc619ec1a70c743e6559938290b7dac3d63b3fd2cf8d6e0646d292a576e8', '439f7d97af4396e0441b7d292045cf78bc22187eec981e5d6fe2ebd0794f975a']],
        ];
    }

    /**
     * @param array{string, string} $point
     */
    #[DataProvider('generatorsUsingSeedAsSeed')]
    public function testGenerator(int $index, array $point): void
    {
        $numbersFromSeed = new IndependentGeneratorGenerator($index, "seed");
        $result = $numbersFromSeed->derive();

        $x = gmp_strval($result->getX(), 16);
        $y = gmp_strval($result->getY(), 16);

        $this->assertEquals($point[0], $x);
        $this->assertEquals($point[1], $y);
    }
}
