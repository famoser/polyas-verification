<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Crypto;

use Famoser\PolyasVerification\Crypto\DER;
use Famoser\PolyasVerification\Crypto\PEM;
use Famoser\PolyasVerification\Crypto\RSA;
use Famoser\PolyasVerification\Crypto\BCMATH;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BCMATHTest extends TestCase
{
    /**
     * @return string[][]
     */
    public static function hexDecProvider(): array
    {
        return [
            ['2', '2'],
            ['a2', '162'],
            ['ba2', '2978'],
        ];
    }

    #[DataProvider('hexDecProvider')]
    public function testExtractPayloadsPublicKeyPem(string $hex, string $dec): void
    {
        $actualDec = BCMATH\Hex::bchexdec($hex);
        $this->assertEquals($dec, $actualDec);

        $actualHex = BCMATH\Hex::bcdechex($dec);
        $this->assertEquals($hex, $actualHex);
    }
}
