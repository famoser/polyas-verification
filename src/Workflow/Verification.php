<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Workflow;

use Famoser\PolyasVerification\Crypto\POLYAS\BallotDecode;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigest;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigestSignature;
use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Crypto\POLYAS\QRCodeDecryption;
use Famoser\PolyasVerification\Crypto\POLYAS\ZKPProofValidation;

readonly class Verification
{
    public const LOGIN_SUCCESSFUL = 'LOGIN_SUCCESSFUL';
    public const DEVICE_PARAMETERS_MATCH = 'DEVICE_PARAMETERS_MATCH';
    public const SIGNATURE_VALID = 'SIGNATURE_VALID';
    public const QR_CODE_DECRYPTION = 'QR_CODE_DECRYPTION';
    public const CHALLENGE_SUCCESSFUL = 'CHALLENGE_SUCCESSFUL';
    public const ZKP_VALID = 'ZKP_VALID';
    public const BALLOT_DECODE = 'BALLOT_DECODE';

    public function __construct(private string $deviceParametersJson, private ApiClient $apiClient)
    {
    }

    /**
     * @param array{
     *     'payload': string,
     *     'voterId': string,
     *     'nonce': string,
     *     'password': string,
     * } $verification
     * @param array{
     *      'fingerprint': string,
     *      'signature': string,
     *  }|null $validReceipt
     */
    public function verify(array $verification, ChallengeCommit $challengeCommit, string &$failedCheck = null, array &$validReceipt = null): string|null
    {
        $challengeCommitment = $challengeCommit->commit();
        $loginPayload = ['voterId' => $verification['voterId'], 'nonce' => $verification['nonce'], 'password' => $verification['password'], 'challengeCommitment' => $challengeCommitment];
        $loginResponse = $this->apiClient->postLogin($loginPayload);
        if (!$loginResponse) {
            $failedCheck = self::LOGIN_SUCCESSFUL;

            return null;
        }

        /** @var array{
         * 'secondDeviceParametersJson': string,
         * 'comSeed': string,
         * 'publicCredential': string,
         * 'ballot': array{
         *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
         *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
         *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
         *      },
         * 'signatureHex': string,
         * 'factorX': string[],
         * 'factorY': string[],
         * 'factorA': string[],
         * 'factorB': string[],
         * } $initialMessage
         */
        $initialMessage = json_decode($loginResponse['initialMessage'], true);
        $deviceParameters = new DeviceParameters($this->deviceParametersJson);
        if (!$deviceParameters->compareDeviceParameters($initialMessage['secondDeviceParametersJson'])) {
            $failedCheck = self::DEVICE_PARAMETERS_MATCH;

            return null;
        }

        $ballotDigest = new BallotDigest($initialMessage, $loginResponse['publicLabel'], $loginResponse['ballotVoterId']);
        $ballotDigestSignature = BallotDigestSignature::createFromBallotDigest($ballotDigest, $initialMessage['signatureHex'], $deviceParameters->getVerificationKey());
        if (!$ballotDigestSignature->verify()) {
            $failedCheck = self::SIGNATURE_VALID;

            return null;
        }

        $validReceipt = $ballotDigestSignature->export();

        $qrCodeDecryption = new QRCodeDecryption($verification['payload'], $ballotDigest, $initialMessage['comSeed']);
        $randomCoinSeed = $qrCodeDecryption->decrypt();
        if (!$randomCoinSeed) {
            $failedCheck = self::QR_CODE_DECRYPTION;

            return null;
        }

        $challengePayload = ['challenge' => $challengeCommit->getEString(), 'challengeRandomCoin' => $challengeCommit->getRString()];
        $challengeResponse = $this->apiClient->postChallenge($challengePayload, $loginResponse['token']);
        if (!$challengeResponse) {
            $failedCheck = self::CHALLENGE_SUCCESSFUL;

            return null;
        }

        $zkpProofValidation = new ZKPProofValidation($initialMessage, $challengeCommit->getE(), $challengeResponse['z'], $deviceParameters->getPublicKey(), $randomCoinSeed);
        if (!$zkpProofValidation->validate()) {
            $failedCheck = self::ZKP_VALID;

            return null;
        }

        $ballotDecoding = new BallotDecode($initialMessage, $deviceParameters->getPublicKey(), $randomCoinSeed);
        $decodedBallot = $ballotDecoding->decode();
        if (!$decodedBallot) {
            $failedCheck = self::BALLOT_DECODE;

            return null;
        }

        return bin2hex($decodedBallot);
    }
}
