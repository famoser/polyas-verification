<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Workflows\Mock;

use Famoser\PolyasVerification\Workflow\Mock\VerificationMock;
use PHPUnit\Framework\TestCase;

class VerificationMockTest extends TestCase
{
    public function testVerificationMock(): void
    {
        $payload = [
            'payload' => '7bgIHYQotKLc8tgCbWp5yuc83xSbN-JV4Vwpnb50qyIzNUj2tYDYzPInG80WJ1mf2tB8BstZXWH_b0y4',
            'voterId' => 'voter3',
            'nonce' => 'f299af96450db626754147aa132237bbf5603df2eea8215a0859288df8015c85',
            'password' => '123456',
        ];
        $this->assertTrue(VerificationMock::isMockPayload($payload));

        $result = VerificationMock::performMockVerification($failedCheck);
        $this->assertNull($failedCheck);
        $this->assertNotNull($result);
    }
}
