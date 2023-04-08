<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Utils;

use Famoser\PolyasVerification\Crypto\POLYAS\BallotEntry;
use Famoser\PolyasVerification\Crypto\POLYAS\Receipt;
use PHPUnit\Framework\TestCase;

class POLYASTest extends TestCase
{
    public function testBallotEntryDigestedBytes(): void
    {
        $ballotEntry = $this->getBallotEntry();
        $expectedDigest = $this->getBallotEntryDigest();

        $digest = BallotEntry::createDigestHex($ballotEntry);
        $this->assertEquals($expectedDigest, $digest);
    }

    public function testBallotEntryFingerprint(): void
    {
        $ballotEntry = $this->getBallotEntry();
        $expectedFingerprint = $this->getBallotEntryFingerprint();

        $fingerprint = BallotEntry::createFingerprint($ballotEntry);
        $this->assertEquals($expectedFingerprint, $fingerprint);
    }

    public function testReceiptValidate(): void
    {
        $receiptPath = $this->getReceiptPath();

        $validationResult = Receipt::validate($receiptPath);
        $this->assertTrue($validationResult[Receipt::RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE]);
        $this->assertFalse($validationResult[Receipt::SIGNATURE_VALID]);
        $this->assertFalse($validationResult[Receipt::FINGERPRINT_REGISTERED]);
    }

    /**
     * @return array{
     *     'publicLabel': string,
     *     'publicCredential': string,
     *     'voterID': string,
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
     *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
     *      }
     *     }
     */
    private function getBallotEntry(): array
    {
        $ballotEntryJson = file_get_contents(__DIR__.'/resources/ballotEntry.json');

        return json_decode($ballotEntryJson, true); // @phpstan-ignore-line
    }

    private function getBallotEntryDigest(): string
    {
        return trim(file_get_contents(__DIR__.'/resources/ballotEntry.json.bytesDigest')); // @phpstan-ignore-line
    }

    private function getBallotEntryFingerprint(): string
    {
        return trim(file_get_contents(__DIR__.'/resources/ballotEntry.json.fingerprint')); // @phpstan-ignore-line
    }

    private function getReceiptPath(): string
    {
        return __DIR__.'/resources/receipt.pdf';
    }
}
