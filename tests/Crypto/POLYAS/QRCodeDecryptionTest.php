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

use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigest;
use Famoser\PolyasVerification\Crypto\POLYAS\QRCode;
use Famoser\PolyasVerification\Crypto\POLYAS\QRCodeDecryption;
use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use PHPUnit\Framework\TestCase;

class QRCodeDecryptionTest extends TestCase
{
    use IncompleteTestTrait;

    public function testCreateComKey(): void
    {
        $comKeyHex = $this->getComKeyHex();
        $qrCodeDecryption = $this->getQRCodeDecryption();

        $actualComKey = $qrCodeDecryption->createComKey();

        $actualComKeyHex = bin2hex($actualComKey);
        $this->assertEquals($comKeyHex, $actualComKeyHex);
    }

    public function testGetContent(): void
    {
        $QRCodeDecryptedHex = $this->getQRCodeDecryptedHex();
        $qrCodeDecryption = $this->getQRCodeDecryption();

        $actualQRCodeDecrypted = $qrCodeDecryption->getContent();

        $actualQRCodeDecryptedHex = bin2hex($actualQRCodeDecrypted);
        $this->assertEquals($QRCodeDecryptedHex, $actualQRCodeDecryptedHex);
    }

    public function testDecrypt(): void
    {
        $comKeyHex = $this->getComKeyHex();
        /** @var string $comKey */
        $comKey = hex2bin($comKeyHex);

        $QRCodeDecryptedHex = $this->getQRCodeDecryptedHex();
        $qrCodeDecryption = $this->getQRCodeDecryption();

        $actualQRCodeDecrypted = $qrCodeDecryption->decrypt($comKey);

        $actualQRCodeDecryptedHex = bin2hex($actualQRCodeDecrypted);
        $this->assertEquals($QRCodeDecryptedHex, $actualQRCodeDecryptedHex);
    }

    private function getQRCodeDecryption(): QRCodeDecryption
    {
        $qrCode = $this->getQRCode();
        $ballotDigest = $this->getBallotDigest();
        $comSeed = $this->getTraceSecondDeviceInitialMsg()['comSeed'];

        return new QRCodeDecryption($qrCode, $ballotDigest, $comSeed);
    }

    /**
     * @return array{
     *     'comSeed': string,
     * }
     */
    private function getTraceSecondDeviceInitialMsg(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/2_LoginResponse_initialMessage.json');

        return json_decode($json, true);
    }

    private function getComKeyHex(): string
    {
        return trim(file_get_contents(__DIR__.'/resources/ballot1/comKey.hex')); // @phpstan-ignore-line
    }

    private function getQRCodeDecryptedHex(): string
    {
        return trim(file_get_contents(__DIR__.'/resources/ballot1/QRCodeDecrypted.hex')); // @phpstan-ignore-line
    }

    private function getBallotDigest(): BallotDigest
    {
        /** @var string $ballotDigestJson */
        $ballotDigestJson = file_get_contents(__DIR__.'/resources/ballot1/ballotEntry.json');

        /** @var array{
         *     'publicLabel': string,
         *     'publicCredential': string,
         *     'voterId': string,
         *     'ballot': array{
         *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
         *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
         *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
         *      }
         *     } $ballotDigestContent
         */
        $ballotDigestContent = json_decode($ballotDigestJson, true);

        return new BallotDigest($ballotDigestContent);
    }

    private function getQRCode(): QRCode
    {
        /** @var string $qrCodeJson */
        $qrCodeJson = file_get_contents(__DIR__.'/resources/ballot1/trace/0_QRcode.json');

        /** @var array{
         *     'c': string,
         *     'vid': string,
         *     'nonce': string
         *     } $content */
        $content = json_decode($qrCodeJson, true);

        return new QRCode($content);
    }
}
