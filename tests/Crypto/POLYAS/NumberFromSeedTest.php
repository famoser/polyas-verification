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

use Famoser\PolyasVerification\Crypto\POLYAS\NumberFromSeed;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NumberFromSeedTest extends TestCase
{
    /**
     * @return array<array<string|int|array<string>>>
     */
    public static function numbersFromSeedProvider(): array
    {
        return [
            ['xyz', 0, ['0']],
            ['xyz', 2, ['0']],
            ['xyz', 7, ['68']],
            ['xyz', 8, ['196']],
            ['xyz', 520, [
                '1732501504205220402900929820446308723705652945081825598593993913145942097001127020633138020218038968109094917857329663184563374015879596834703721749398989648',
                '2207401303665503434031531355511922974889692817601183500259263742625914061046146142929376778072827450461936300533206904979740474482058840003720379960491023511',
                '1883889587903519477357838514223953979954201344665681798367023196328721975720052153913582122151913785273222921786889836987731296728825119604809609410157987402',
                '1423259849467217711185874799515607842842602785767879766623736284680209832704638390900412597196948750015976271793930713744890547611655064835165883323889981463',
            ]],
        ];
    }

    /**
     * @param string[] $results
     */
    #[DataProvider('numbersFromSeedProvider')]
    public function testNumber(string $seed, int $bitLength, array $results): void
    {
        $numbersFromSeed = new NumberFromSeed($seed, $bitLength);

        foreach ($results as $result) {
            $number = $numbersFromSeed->number();
            $numberStr = gmp_strval($number);
            $this->assertEquals($result, $numberStr);
            $numbersFromSeed = $numbersFromSeed->iterate();
        }
    }
}
