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

use Famoser\PolyasVerification\Crypto\POLYAS\BallotEntry;
use Famoser\PolyasVerification\Crypto\POLYAS\Receipt;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BallotEntryTest extends TestCase
{
    /**
     * @return string[]
     */
    public static function ballotProvider(): array
    {
        return [['ballot1']];
    }

    #[DataProvider('ballotProvider')]
    public function testBallotEntryDigestedBytes(string $ballot): void
    {
        $ballotEntry = $this->getBallotEntry($ballot);
        $expectedDigest = $this->getBallotEntryDigest($ballot);

        $digest = BallotEntry::createDigestHex($ballotEntry);
        $this->assertEquals($expectedDigest, $digest);
    }

    #[DataProvider('ballotProvider')]
    public function testBallotEntryFingerprint(string $ballot): void
    {
        $ballotEntry = $this->getBallotEntry($ballot);
        $expectedFingerprint = $this->getBallotEntryFingerprint($ballot);

        $fingerprint = BallotEntry::createFingerprint($ballotEntry);
        $this->assertEquals($expectedFingerprint, $fingerprint);
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
    private function getBallotEntry(string $ballot): array
    {
        $ballotEntryJson = file_get_contents(__DIR__.'/resources/'.$ballot.'/ballotEntry.json');

        return json_decode($ballotEntryJson, true); // @phpstan-ignore-line
    }

    private function getBallotEntryDigest(string $ballot): string
    {
        return trim(file_get_contents(__DIR__.'/resources/'.$ballot.'/ballotEntry.json.bytesDigest')); // @phpstan-ignore-line
    }

    private function getBallotEntryFingerprint(string $ballot): string
    {
        return trim(file_get_contents(__DIR__.'/resources/'.$ballot.'/ballotEntry.json.fingerprint')); // @phpstan-ignore-line
    }
}
