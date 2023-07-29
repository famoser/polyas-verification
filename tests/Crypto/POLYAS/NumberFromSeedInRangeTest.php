<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\POLYAS\NumbersFromSeedInRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NumberFromSeedInRangeTest extends TestCase
{
    /**
     * @return array<array<string|int|array<string>>>
     */
    public static function numbersFromSeedInRangeProvider(): array
    {
        return [
            ['xyz', '1732501504205220402900929820446308723705652945081825598593993913145942097001127020633138020218038968109094917857329663184563374015879596834703721749398989649', [
                '1732501504205220402900929820446308723705652945081825598593993913145942097001127020633138020218038968109094917857329663184563374015879596834703721749398989648',
                '1423259849467217711185874799515607842842602785767879766623736284680209832704638390900412597196948750015976271793930713744890547611655064835165883323889981463',
            ]],
        ];
    }

    /**
     * @param string[] $results
     */
    #[DataProvider('numbersFromSeedInRangeProvider')]
    public function testNumber(string $seed, string $maxNumber, array $results): void
    {
        $maxNumberGmp = gmp_init($maxNumber);
        $numbersFromSeedInRange = new NumbersFromSeedInRange(count($results), $seed, $maxNumberGmp);
        $numbers = $numbersFromSeedInRange->numbers();

        for ($i = 0; $i < count($results); ++$i) {
            $result = $results[$i];
            $numberStr = gmp_strval($numbers[$i]);
            $this->assertEquals($result, $numberStr);
        }
    }
}
