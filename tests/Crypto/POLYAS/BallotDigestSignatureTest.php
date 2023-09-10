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
use PHPUnit\Framework\TestCase;

class BallotDigestSignatureTest extends TestCase
{
    public function testBallotDigestSignature(): void
    {
        $ballotDigestSignature = $this->getBallotDigestSignature();

        $this->assertTrue($ballotDigestSignature->verify());
    }

    public function testBallotDigestSignatureExport(): void
    {
        $ballotDigestSignature = $this->getBallotDigestSignature();

        $export = $ballotDigestSignature->export();

        $this->assertEquals('91dd5f592932c7c681f20310c801e7ea935f116527b65ce6524f14c6ad2f9dac', $export['fingerprint']);
        $this->assertEquals('529f3e8c7d1f0e2c8061526d8e1d8000c24ab60b32b3bda0ce959788483f977fb12da70ccb7ac154a698ef925cf7ca52e142f8eb22d23e5ccd42b63da227230bf886b13211f5c1f618a946a64f8566fd36849b46a156d4a35288204fd7b22e15fcdce8884b5d6e5c69b07ca271332ba14eced079402c735db642b82ae7478fe2efe849d8c50ba11b7d6985486607a54ea42c6394dc2060ac58cfa9c69cc750816dad43fb74d113ab7bc014e619649688fdbf96a29c894fa2cfc5d2bac8b897d0c8dbb3b79e5c17a90913dcb4ba583ea90e706891d38278745c1b4856f88d045c38b840d4fd427291187c250b2ed7bc846fa25440e98d3e9832f2047e52bc5207', $export['signature']);

        $verificationKey = $this->getDeviceParameters()->getVerificationKey();
        $ballotDigestSignature2 = BallotDigestSignature::createFromExport($export, $verificationKey);
        $this->assertTrue($ballotDigestSignature2->verify());
    }

    public function testVerificationKeyEncoding(): void
    {
        $verificationKey = $this->getDeviceParameters()->getVerificationKey();

        /** @var string $verificationKeyBin */
        $verificationKeyBin = hex2bin($verificationKey);
        $publicKey = DER\Decoder::asRSAPublicKey($verificationKeyBin);

        $nBitLength = strlen(gmp_strval($publicKey->getN(), 2));
        $this->assertEquals(2048, $nBitLength);

        $eBitLength = strlen(gmp_strval($publicKey->getE(), 2));
        $this->assertEquals(17, $eBitLength);

        $publicKeyPem = PEM\Encoder::encode('PUBLIC KEY', $verificationKeyBin);
        $publicKey = openssl_get_publickey($publicKeyPem);
        $this->assertNotEquals(false, $publicKey);
        openssl_error_string(); // need this to empty the openssl error queue due to #11054
    }

    private function getBallotDigestSignature(): BallotDigestSignature
    {
        $ballotDigest = $this->getBallotDigest();
        $signatureHex = $this->getTraceSecondDeviceInitialMsg()['signatureHex'];
        $verificationKey = $this->getDeviceParameters()->getVerificationKey();

        return BallotDigestSignature::createFromBallotDigest($ballotDigest, $signatureHex, $verificationKey);
    }

    private function getBallotDigest(): BallotDigest
    {
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
        $ballotDigestContent = json_decode($ballotDigestJson, true); // @phpstan-ignore-line

        return new BallotDigest($ballotDigestContent, $ballotDigestContent['publicLabel'], $ballotDigestContent['voterId']);
    }

    private function getDeviceParameters(): DeviceParameters
    {
        /** @var string $deviceParametersJson */
        $deviceParametersJson = file_get_contents(__DIR__.'/resources/ballot1/deviceParameters/deviceParameters.json');

        return new DeviceParameters($deviceParametersJson);
    }

    /**
     * @return array{
     *     'signatureHex': string,
     * }
     */
    private function getTraceSecondDeviceInitialMsg(): array
    {
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/2_LoginResponse_initialMessage.json');

        return json_decode($json, true); // @phpstan-ignore-line
    }
}
