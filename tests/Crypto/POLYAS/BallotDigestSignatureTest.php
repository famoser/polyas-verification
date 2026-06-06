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

use Famoser\PolyasVerification\Crypto\DER;
use Famoser\PolyasVerification\Crypto\PEM;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigest;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigestSignature;
use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use PHPUnit\Framework\TestCase;

class BallotDigestSignatureTest extends TestCase
{
    public function testBallotDigestSignature(): void
    {
        $message = Ballot0::getLoginResponseInitialMessage();
        $ballotDigest = new BallotDigest($message['ballot']);

        $deviceParameters = Ballot0::getDeviceParameters();
        $ballotDigestSignature = BallotDigestSignature::createFromBallotDigest($ballotDigest, $message['signatureHex'], $deviceParameters->getVerificationKey());

        $this->assertTrue($ballotDigestSignature->verify());

        $exported = $ballotDigestSignature->export();
        $ballotDigestSignatureFromExport = BallotDigestSignature::createFromExport($exported, $deviceParameters->getVerificationKey());
        $this->assertTrue($ballotDigestSignatureFromExport->verify());
    }

    public function testVerificationKeyEncoding(): void
    {
        $deviceParameters = Ballot0::getDeviceParameters();

        /** @var string $verificationKeyBin */
        $verificationKeyBin = hex2bin($deviceParameters->getVerificationKey());
        $publicKey = DER\Decoder::asRSAPublicKey($verificationKeyBin);

        $nBitLength = strlen(gmp_strval($publicKey->getN(), 2));
        $this->assertEquals(3072, $nBitLength);

        $eBitLength = strlen(gmp_strval($publicKey->getE(), 2));
        $this->assertEquals(17, $eBitLength);

        $publicKeyPem = PEM\Encoder::encode('PUBLIC KEY', $verificationKeyBin);
        $publicKey = openssl_get_publickey($publicKeyPem);
        $this->assertNotEquals(false, $publicKey);
    }
}
