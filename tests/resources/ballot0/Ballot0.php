<?php

namespace Famoser\PolyasVerification\Test\resources\ballot0;

use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;

class Ballot0
{
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
        $loginResponseJson = file_get_contents(__DIR__ . '/2_LoginResponse.json');
        $loginResponse = json_decode($loginResponseJson, true);

        $initialMessageJson = $loginResponse['value']['initialMessage'];
        return json_decode($initialMessageJson, true);
    }

    public static function getBallotReference(): string
    {
        return self::getLoginResponseInitialMessage()['ballot']['reference'];
    }

    public static function getComSeed(): string
    {
        return self::getLoginResponseInitialMessage()['comSeed'];
    }

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

    public static function getBallotDigest(): string
    {
        return trim(file_get_contents(__DIR__ . '/ballot.digest'));
    }

    public static function getDeviceParameters(): DeviceParameters
    {
        $json = file_get_contents(__DIR__ . '/deviceParameters.json');

        return DeviceParameters::createFromFingerprintedJson($json);
    }
}
