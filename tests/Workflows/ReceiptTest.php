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

use Famoser\PolyasVerification\Workflow\Receipt;
use PHPUnit\Framework\TestCase;

class ReceiptTest extends TestCase
{
    public function testReceiptVerify(): void
    {
        $receiptPath = $this->getReceiptPath();

        $receipt = new Receipt();
        $validationResult = $receipt->verify($receiptPath);
        $this->assertTrue($validationResult[Receipt::RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE]);
        $this->assertFalse($validationResult[Receipt::SIGNATURE_VALID]);
        $this->assertFalse($validationResult[Receipt::FINGERPRINT_REGISTERED]);
    }

    private function getReceiptPath(): string
    {
        return __DIR__ . '/resources/ballot0/receipt.pdf';
    }
}