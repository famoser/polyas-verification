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
        $this->markTestIncompleteNS('Ballot existence check not implemented yet.');

        $receiptPath = $this->getReceiptPath();
        $deviceParameters = json_decode(file_get_contents(__DIR__.'/resources/ballot0/deviceParameters.json'), true); // @phpstan-ignore-line
        /** @var string $verificationKeyX509 */
        $verificationKeyX509 = hex2bin($deviceParameters['verificationKey']);

        $receipt = new Receipt($verificationKeyX509);
        $result = $receipt->verify($receiptPath, $failedCheck);
        $this->assertTrue($result);
        $this->assertNull($failedCheck);
    }

    public function testReceiptRawVerify(): void
    {
        $receiptPath = $this->getReceiptRawPath();

        $receipt = new Receipt('');
        $result = $receipt->getFingerprintAndSignature($receiptPath, $fingerprint, $signature);
        $this->assertTrue($result);
        $this->assertNotNull($fingerprint);
        $this->assertNotNull($signature);
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
