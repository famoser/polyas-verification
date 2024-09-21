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

use Famoser\PolyasVerification\Workflow\Mock\DownloadReceiptMock;
use PHPUnit\Framework\TestCase;

class DownloadReceiptMockTest extends TestCase
{
    public function testVerificationMock(): void
    {
        $payload = DownloadReceiptMock::createMockPayload();
        $this->assertTrue(DownloadReceiptMock::isMockPayload($payload));

        $result = DownloadReceiptMock::performMockDownloadReceipt($payload, $pdf, $failedCheck);
        $this->assertTrue($result);
        $this->assertNull($failedCheck);
        $this->assertNotNull($pdf);
    }
}
