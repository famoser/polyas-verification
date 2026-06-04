<?php

namespace Famoser\PolyasVerification\Test\resources\ballot0;

class Ballot0
{
    /**
     * @return array{comSeed: string, 'ballot': array{encryptedChoice: mixed, zkp: mixed, reference: string, publicLabel: string}}
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
}
