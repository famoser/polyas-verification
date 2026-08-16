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

use Famoser\PolyasVerification\Crypto\POLYAS\BallotAssociation;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDecode;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigest;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigestSignature;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotReceipt;
use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Crypto\POLYAS\QRCodeDecryption;
use Famoser\PolyasVerification\Crypto\POLYAS\ZKPProofValidation;
use Famoser\PolyasVerification\Storage;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @phpstan-type ValidReceipt array{
 *     fingerprint: string,
 *     signature: string,
 *     ballotVoterId: string,
 * }
 */
readonly class Verification
{
    public const string LOGIN_SUCCESSFUL = 'LOGIN_SUCCESSFUL';
    public const string DEVICE_PARAMETERS_MATCH = 'DEVICE_PARAMETERS_MATCH';
    public const string ASSOCIATION_VALID = 'ASSOCIATION_VALID';
    public const string SIGNATURE_VALID = 'SIGNATURE_VALID';
    public const string QR_CODE_DECRYPTION = 'QR_CODE_DECRYPTION';
    public const string CHALLENGE_SUCCESSFUL = 'CHALLENGE_SUCCESSFUL';
    public const string ZKP_VALID = 'ZKP_VALID';
    public const string BALLOT_DECODE = 'BALLOT_DECODE';

    public function __construct(private DeviceParameters $deviceParameters, private ApiClient $apiClient, private string $polyasElection)
    {
    }

    /**
     * @param array{
     *    'c': string,
     *    'd': string,
     *    'vid': string,
     *    'nonce': string
     *   } $payload
     * @param ValidReceipt|null $validReceipt
     *
     * @phpstan-assert-if-false string $failedCheck
     * @phpstan-assert-if-true ValidReceipt $validReceipt
     * @phpstan-assert-if-true string $hexBallot
     */
    public function verify(array $payload, string $password, ChallengeCommit $challengeCommit, ?array &$validReceipt = null, ?string &$hexBallot = null, ?string &$failedCheck = null): bool
    {
        $challengeCommitment = $challengeCommit->commit();
        $loginPayload = ['voterId' => $payload['vid'], 'ballotReference' => $payload['d'], 'nonce' => $payload['nonce'], 'password' => $password, 'challengeCommitment' => $challengeCommitment];
        try {
            $loginResponse = $this->apiClient->postLogin($loginPayload);
        } catch (GuzzleException) {
            $loginResponse = null;
        }
        if (!$loginResponse) {
            $failedCheck = self::LOGIN_SUCCESSFUL;

            return false;
        }

        /** @var array{
         * 'secondDeviceParametersJson': string,
         * 'comSeed': string,
         * 'ballot': array{
         *    'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
         *    'zkp': array{array{'c': numeric-string, 'f': numeric-string}},
         *    'publicLabel': string,
         *    'reference': string,
         *    'signature': array{'c': numeric-string, 'f': numeric-string},
         * },
         * 'signatureHex': string,
         * 'factorX': string[],
         * 'factorY': string[],
         * 'factorA': string[],
         * 'factorB': string[],
         * } $initialMessage
         */
        $initialMessage = json_decode($loginResponse['initialMessage'], true);
        if (!$this->deviceParameters->compareDeviceParameters($initialMessage['secondDeviceParametersJson'])) {
            $failedCheck = self::DEVICE_PARAMETERS_MATCH;

            return false;
        }

        $qrCodeDecryption = new QRCodeDecryption($payload['c'], $payload['d'], $initialMessage['comSeed']);
        if (!$qrCodeDecryption->decrypt($randomCoinSeed, $referenceCoin)) {
            $failedCheck = self::QR_CODE_DECRYPTION;

            return false;
        }

        $ballotDigest = new BallotDigest($initialMessage['ballot']);
        $ballotDigestSignature = BallotDigestSignature::createFromBallotDigest($ballotDigest, $initialMessage['signatureHex'], $this->deviceParameters->getVerificationKey());
        if (!$ballotDigestSignature->verify()) {
            $failedCheck = self::SIGNATURE_VALID;

            return false;
        }

        $ballotAssociation = new BallotAssociation($initialMessage['ballot']['reference'], $referenceCoin, $payload['vid']);
        if (!$ballotAssociation->verify()) {
            $failedCheck = self::ASSOCIATION_VALID;

            return false;
        }

        $ballotReceipt = new BallotReceipt($ballotDigestSignature, $loginResponse['ballotVoterId']);
        $validReceipt = $ballotReceipt->export();

        $challengePayload = ['challenge' => $challengeCommit->getEString(), 'challengeRandomCoin' => $challengeCommit->getRString()];
        try {
            $challengeResponse = $this->apiClient->postChallenge($challengePayload, $loginResponse['token']);
        } catch (GuzzleException) {
            $challengeResponse = null;
        }

        if (!$challengeResponse) {
            $failedCheck = self::CHALLENGE_SUCCESSFUL;

            return false;
        }

        $zkpProofValidation = new ZKPProofValidation($initialMessage, $challengeCommit->getE(), $challengeResponse['z'], $this->deviceParameters->getPublicKey(), $randomCoinSeed);
        if (!$zkpProofValidation->validate()) {
            $failedCheck = self::ZKP_VALID;

            return false;
        }

        $ballotDecoding = new BallotDecode($initialMessage, $this->deviceParameters->getPublicKey(), $randomCoinSeed);
        $decodedBallot = $ballotDecoding->decode();
        if (!$decodedBallot) {
            $failedCheck = self::BALLOT_DECODE;

            return false;
        }

        $hexBallot = bin2hex($decodedBallot);

        return true;
    }
}
