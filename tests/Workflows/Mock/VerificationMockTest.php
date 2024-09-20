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
        $payload = VerificationMock::createMockPayload();
        $this->assertTrue(VerificationMock::isMockPayload($payload));

        $result = VerificationMock::performMockVerification($payload, $failedCheck);
        $this->assertNull($failedCheck);
        $this->assertNotNull($result);
    }
}
