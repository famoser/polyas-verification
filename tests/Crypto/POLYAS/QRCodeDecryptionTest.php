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

use Famoser\PolyasVerification\Crypto\POLYAS\QRCodeDecryption;
use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use PHPUnit\Framework\TestCase;

class QRCodeDecryptionTest extends TestCase
{
    public function testCreateComKey(): void
    {
        $comSeed = Ballot0::getComSeed();
        $qrCode = Ballot0::getQRCode();

        $qrCodeDecryption = new QRCodeDecryption($qrCode['c'], $qrCode['d'], $comSeed);

        $actualComKey = $qrCodeDecryption->createComKey();
        $actualComKeyHex = bin2hex($actualComKey);
        $this->assertEquals('1949d15227f4f73a9cf4e2dab50983373c63dfd7a4a8f22bad14310df4f666b1', $actualComKeyHex);
    }

    public function testDecryptValue(): void
    {
        $comSeed = Ballot0::getComSeed();
        $qrCode = Ballot0::getQRCode();
        $qrCodeDecrypted = Ballot0::getQRCodeDecrypted();

        $qrCodeDecryption = new QRCodeDecryption($qrCode['c'], $qrCode['d'], $comSeed);

        $comKey = $qrCodeDecryption->createComKey();
        $actualC = QRCodeDecryption::decryptValue($comKey, $qrCode['c']);
        $this->assertEquals($qrCodeDecrypted['randomCoinSeed'], bin2hex($actualC));
        $actualD = QRCodeDecryption::decryptValue($comKey, $qrCode['d']);
        $this->assertEquals($qrCodeDecrypted['referenceCoin'], $actualD);
    }

    public function testDecrypt(): void
    {
        $comSeed = Ballot0::getComSeed();
        $qrCode = Ballot0::getQRCode();
        $qrCodeDecrypted = Ballot0::getQRCodeDecrypted();

        $qrCodeDecryption = new QRCodeDecryption($qrCode['c'], $qrCode['d'], $comSeed);
        $status = $qrCodeDecryption->decrypt($randomCoinSeed, $referenceCoin);
        $this->assertTrue($status);
        $this->assertEquals($qrCodeDecrypted['randomCoinSeed'], bin2hex($randomCoinSeed));
        $this->assertEquals($qrCodeDecrypted['referenceCoin'], $referenceCoin);
    }
}
