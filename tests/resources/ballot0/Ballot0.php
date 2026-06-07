<?php

namespace Famoser\PolyasVerification\Test\resources\ballot0;

use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;

class Ballot0
{
    /**
     * @return array{
     *   'encC': string,
     *   'encD': string,
     *   'vid': string,
     *   'nonce': string
     *  }
     */
    public static function getQRCode(): array
    {
        $qrCodeJson = file_get_contents(__DIR__ . '/0_QRcode.json');

        return json_decode($qrCodeJson, true);
    }

    /**
     * @return array{
     *   'referenceCoin': string,
     *   'randomCoinSeed': string,
     *  }
     */
    public static function getQRCodeDecrypted(): array
    {
        $json = file_get_contents(__DIR__ . '/0_QRcode_decrypted.json');

        return json_decode($json, true);
    }

    /**
     * @return array{
     *   'voterId': string,
     *   'ballotReference': string,
     *   'nonce': string,
     *   'password': string,
     *   'challengeCommitment': string
     *  }
     */
    public static function getLoginRequest(): array
    {
        $loginRequestJson = file_get_contents(__DIR__ . '/1_LoginRequest.json');

        return json_decode($loginRequestJson, true);
    }
    /**
     * @return array{
     *   'value': array{'token': string,}
     * }
     */
    public static function getLoginResponse(): array
    {
        $loginResponseJson = file_get_contents(__DIR__ . '/2_LoginResponse.json');
        return json_decode($loginResponseJson, true);
    }

    /**
     * @return array{
     *  'secondDeviceParametersJson': string,
     *  'comSeed': string,
     *  'ballot': array{
     *     'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *     'zkp': array{array{'c': numeric-string, 'f': numeric-string}},
     *     'publicLabel': string,
     *     'reference': string,
     *     'signature': array{'c': numeric-string, 'f': numeric-string},
     *  },
     *  'signatureHex': string,
     *  'factorX': string[],
     *  'factorY': string[],
     *  'factorA': string[],
     *  'factorB': string[],
     * }
     */
    public static function getLoginResponseInitialMessage(): array
    {
        $loginResponse = self::getLoginResponse();

        $initialMessageJson = $loginResponse['value']['initialMessage'];
        return json_decode($initialMessageJson, true);
    }

    /**
     * @return array{'challenge': string, 'challengeRandomCoin': string}
     */
    public static function getChallengeRequest(): array
    {
        $json = file_get_contents(__DIR__ . '/3_ChallengeRequest.json');

        return json_decode($json, true);
    }

    /**
     * @return array{'z': numeric-string[]}
     */
    public static function getChallengeResponse(): array
    {
        $json = file_get_contents(__DIR__ . '/4_ChallengeResponse.json');

        return json_decode($json, true);
    }

    public static function getBallotReference(): string
    {
        return self::getLoginResponseInitialMessage()['ballot']['reference'];
    }

    public static function getComSeed(): string
    {
        return self::getLoginResponseInitialMessage()['comSeed'];
    }

    public static function getBallotDigest(): string
    {
        return trim(file_get_contents(__DIR__ . '/ballot.digest'));
    }

    public static function getDeviceParameters(): DeviceParameters
    {
        $json = file_get_contents(__DIR__ . '/deviceParameters.json');

        return DeviceParameters::createFromFingerprintedJson($json);
    }

    public static function getRandomCoinSeed(): string
    {
        $randomCoinSeed = Ballot0::getQRCodeDecrypted()['randomCoinSeed'];

        return hex2bin($randomCoinSeed);
    }

    public static function getReceiptPath(): string
    {
        return __DIR__ . '/receipt.pdf';
    }
}
