<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Workflows;

use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use Famoser\PolyasVerification\Workflow\Receipt;
use PHPUnit\Framework\TestCase;

class ReceiptTest extends TestCase
{
    use IncompleteTestTrait;
    public function testReceiptVerify(): void
    {
        $this->markTestIncompleteNS('Signature validation fails');

        $receiptPath = $this->getReceiptPath();

        $receipt = new Receipt();
        $result = $receipt->verify($receiptPath, $failedCheck);
        $this->assertTrue($result);
        $this->assertNull($failedCheck);
    }

    public function testReceiptRawVerify(): void
    {
        $this->markTestIncompleteNS('Signature validation fails');

        $receiptPath = $this->getReceiptRawPath();

        $receipt = new Receipt();
        $result = $receipt->verify($receiptPath, $failedCheck);
        $this->assertTrue($result);
        $this->assertNull($failedCheck);
    }

    private function getReceiptPath(): string
    {
        return __DIR__ . '/resources/ballot0/receipt.pdf';
    }

    private function getReceiptRawPath(): string
    {
        return __DIR__ . '/resources/vote-receipt-raw.pdf';
    }
}
