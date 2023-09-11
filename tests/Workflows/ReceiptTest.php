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

use Famoser\PolyasVerification\Workflow\VerifyReceipt;
use PHPUnit\Framework\TestCase;

class ReceiptTest extends TestCase
{
    public function testReceiptVerify(): void
    {
        $receiptPath = $this->getReceiptPath();
        $deviceParameters = $this->getDeviceParameters();

        $receipt = new VerifyReceipt($deviceParameters['verificationKey']);
        $result = $receipt->verify($receiptPath, $failedCheck);
        $this->assertTrue($result);
        $this->assertNull($failedCheck);
    }

    public function testReceiptRawVerify(): void
    {
        $receiptPath = $this->getReceiptRawPath();
        $deviceParameters = $this->getDeviceParameters();

        $receipt = new VerifyReceipt($deviceParameters['verificationKey']);
        $result = $receipt->getFingerprintAndSignature($receiptPath, $fingerprint, $signature);
        $this->assertTrue($result);
        $this->assertNotNull($fingerprint);
        $this->assertNotNull($signature);
    }

    /**
     * @return array{'verificationKey': string}
     */
    private function getDeviceParameters(): array
    {
        return json_decode(file_get_contents(__DIR__.'/resources/ballot0/deviceParameters.json'), true); // @phpstan-ignore-line
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
