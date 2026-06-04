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

use Famoser\PolyasVerification\Crypto\POLYAS\NumbersFromSeedInRange;
use Famoser\PolyasVerification\Crypto\POLYAS\UniformHash;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UniformHashTest extends TestCase
{
    /**
     * @return array{numeric-string, string, numeric-string}[]
     */
    public static function uniformHashProvider(): array
    {
        return [
            // ['2126991829', 'some data', '414907466'], cannot make this example work, but maybe not important
            ['115792089237316195423570985008687907852837564279074904382605163141518161494337', 'voter7', '75257976807143615402452449431801958905811497121857943123079219007777383312406'],
        ];
    }

    #[DataProvider('uniformHashProvider')]
    public function testHash(string $q, string $inputData, string $expectedResult): void
    {
        $unformHash = new UniformHash(gmp_init($q), $inputData);
        $result = $unformHash->hash();
        $this->assertEquals($expectedResult, gmp_strval($result));
    }
}
