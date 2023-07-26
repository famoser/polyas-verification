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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BallotEntryTest extends TestCase
{
    /**
     * @return string[][]
     */
    public static function bcdecProvider(): array
    {
        return [
            // ballot0
            ['101884463475449792123435889591575651216237787976278053756254664400615609849402', '00e1409011d385f929f77566ed7bbf9e60677ed5946f7872931f0ae564c4b1d63a'],
            ['81174399198084050819276673106811016645306940417447240994496938329622216775741', '00b37714efd6c1c9cedaa0b3eb8c8d53c9aaec3e5e1785073214b6623ae975183d'],
            ['74448678640965672610706594238871799913455885364649021819048000651594839644426', '00a498757741971017420083c6c2ebd0e6718c03d4e168dbd3f7859ed13894590a'],
            ['33276182696803028530168279419966710636866170477238993593368673329284148491287', '4991a6e74dc5ed7012c9aa1a08d9e1b75a49e839abedff068b94419e5fe9f417'],

            // ballot1
            ['79966540728819921955585823592173536360716995948664894735154654897488787881072', '00b0cb75473491d930dfffdf51f65753db9e6d1252720f50532bd6a4ddb5073c70'],
            ['90388416755735603296616014607154433872748203957820626540975447356971608146868', '00c7d607e9d00ebb3849a3632d1e64bdc726ea3ba0ce564a0de2c578f1d5db83b4'],
            ['4219105992081372606513358125198075081967495840895255912931536426010398533192', '0953edeaf6598b16e39aab05f7a751a5d68c0190ef6c10b64b602b6a97c1a648'],
            ['110464010855198853861051741469261963282081696331616030540127604123885412224008', '00f4386a1cefe2f2ef00aef6b4cc107ec5ec13984f65e1c941fdf49882986f0c08'],
        ];
    }

    #[DataProvider('bcdecProvider')]
    public function testBallotEntryBCDEC(string $dec, string $hex): void
    {
        $actualHex = BallotEntry::bcdechex($dec);
        $this->assertEquals($hex, $actualHex);
    }

    /**
     * @return string[][]
     */
    public static function ballotProvider(): array
    {
        return [
            ['ballot0'],
            ['ballot1'],
        ];
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
     *     'voterId': string,
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
        /** @var string $fileContent */
        $fileContent = file_get_contents(__DIR__.'/resources/'.$ballot.'/ballotEntry.json.bytesDigest');
        $singleLine = str_replace("\n", '', $fileContent);

        return trim($singleLine);
    }

    private function getBallotEntryFingerprint(string $ballot): string
    {
        return trim(file_get_contents(__DIR__.'/resources/'.$ballot.'/ballotEntry.json.fingerprint')); // @phpstan-ignore-line
    }
}
