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

use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use Famoser\PolyasVerification\Workflow\VerifyReceipt;
use PHPUnit\Framework\TestCase;

class ReceiptTest extends TestCase
{
    public function testReceiptVerify(): void
    {
        $receiptPath = Ballot0::getReceiptPath();
        $deviceParameters = Ballot0::getDeviceParameters();

        $receipt = new VerifyReceipt($deviceParameters->getVerificationKey());

        $result = $receipt->getFingerprintAndSignature($receiptPath, $fingerprint, $signature);
        $this->assertTrue($result);
        $this->assertNotNull($fingerprint);
        $this->assertNotNull($signature);

        $result = $receipt->verify($receiptPath, $validReceipt, $failedCheck);
        $this->assertTrue($result);
        $this->assertNull($failedCheck);
    }
}
