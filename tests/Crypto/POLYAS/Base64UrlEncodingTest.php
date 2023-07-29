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

use Famoser\PolyasVerification\Crypto\POLYAS\Base64UrlEncoding;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Base64UrlEncodingTest extends TestCase
{
    public static function base64Provider(): array
    {
        return [
            ['PDw/Pz8+Pg==', 'PDw_Pz8-Pg'],
            ['vtWXj+YxxTV2ektefJ5pk7AWc9saoPbu6wJZUZ9R1t8ekU89x7SCYLcg8ODi3fHST4BTmAK97XN3XqWc', 'vtWXj-YxxTV2ektefJ5pk7AWc9saoPbu6wJZUZ9R1t8ekU89x7SCYLcg8ODi3fHST4BTmAK97XN3XqWc'],
        ];
    }

    #[DataProvider('base64Provider')]
    public function testEncodeDecode(string $base64, string $base64URL): void
    {
        $actualBase64URL = Base64UrlEncoding::encode($base64);
        $this->assertEquals($base64URL, $actualBase64URL);

        $actualBase64 = Base64UrlEncoding::decode($base64URL);
        $this->assertEquals($base64, $actualBase64);
    }
}
