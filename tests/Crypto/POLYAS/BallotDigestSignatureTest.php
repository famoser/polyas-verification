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
use Famoser\PolyasVerification\Crypto\RSA\OpenSSLException;
use PHPUnit\Framework\TestCase;

class BallotDigestSignatureTest extends TestCase
{
    public function testBallotDigestSignatureDOESNOTVERIFY(): void
    {
        $ballot = 'ballot1';
        $ballotDigestSignature = $this->getBallotDigestSignature();

        $this->expectException(OpenSSLException::class);
        $ballotDigestSignature->verify();
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

        return new BallotDigestSignature($ballotDigest, $signatureHex, $verificationKey);
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

        return new BallotDigest($ballotDigestContent);
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
