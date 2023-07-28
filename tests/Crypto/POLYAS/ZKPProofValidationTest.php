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
use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Crypto\POLYAS\QRCode;
use Famoser\PolyasVerification\Crypto\POLYAS\ZKPProofValidation;
use PHPUnit\Framework\TestCase;

class ZKPProofValidationTest extends TestCase
{
    public function testValidate(): void
    {
        $ZKPProofValidation = $this->getZKPProofValidation();

        $this->assertTrue($ZKPProofValidation->validate());
    }

    private function getZKPProofValidation(): ZKPProofValidation
    {
        $deviceParameters = $this->getDeviceParameters();
        $payload = $this->getTraceSecondDeviceInitialMsg();
        $request = $this->getTraceChallengeRequest();
        $response = $this->getTraceChallengeResponseValue();

        return new ZKPProofValidation($payload, $response['z'], $request['challenge'], $deviceParameters->getPublicKey());
    }

    private function getDeviceParameters(): DeviceParameters
    {
        $deviceParametersPayload = $this->getTraceSecondDeviceInitialMsg();
        $deviceParametersJson = $deviceParametersPayload['secondDeviceParametersJson'];

        return new DeviceParameters($deviceParametersJson);
    }

    /**
     * @return array{
     *     'factorA': string[],
     *     'factorB': string[],
     *     'factorX': string[],
     *     'factorY': string[],
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *      }
     *     }
     */
    private function getTraceSecondDeviceInitialMsg(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/2_LoginResponse_initialMessage.json');

        return json_decode($json, true);
    }

    /**
     * @return array{
     *     'challenge': string,
     *     'challengeRandomCoin': string,
     * }
     */
    private function getTraceChallengeRequest(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/3_ChallengeRequest.json');

        return json_decode($json, true);
    }

    /**
     * @return array{
     *     'z': string[],
     * }
     */
    private function getTraceChallengeResponseValue(): array
    {
        /** @var string $json */
        $json = file_get_contents(__DIR__.'/resources/ballot1/trace/4_ChallengeResponse_value.json');

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
