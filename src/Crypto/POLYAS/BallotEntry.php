<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\POLYAS;

class BallotEntry
{
    /**
     * @param array{
     *     'publicLabel': string,
     *     'publicCredential': string,
     *     'voterID': string,
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
     *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
     *      }
     *     } $ballotEntry
     */
    public static function createFingerprint(array $ballotEntry): string
    {
        $digestHex = self::createDigestHex($ballotEntry);
        $digest = hex2bin($digestHex);
        if (!$digest) {
            throw new \RuntimeException('Cannot transform digest hex to binary');
        }

        return hash('sha256', $digest);
    }

    /**
     * @param array{
     *     'publicLabel': string,
     *     'publicCredential': string,
     *     'voterID': string,
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
     *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
     *      }
     *     } $ballotEntry
     */
    public static function createDigestHex(array $ballotEntry): string
    {
        $publicLabel = $ballotEntry['publicLabel'];
        $publicCredential = $ballotEntry['publicCredential'];
        $voterID = $ballotEntry['voterID'];

        $content = self::getStringHexLength4Bytes($publicLabel).bin2hex($publicLabel);
        $content .= self::getBytesHexLength4Bytes($publicCredential).$publicCredential;
        $content .= self::getStringHexLength4Bytes($voterID).bin2hex($voterID);

        $ballot = $ballotEntry['ballot'];
        $ciphertexts = $ballot['encryptedChoice']['ciphertexts'];
        $content .= self::getCollectionHexLength4Bytes($ciphertexts);
        foreach ($ciphertexts as $ciphertext) {
            $content .= self::getBytesHexLength4Bytes($ciphertext['x']).$ciphertext['x'];
            $content .= self::getBytesHexLength4Bytes($ciphertext['y']).$ciphertext['y'];
        }

        // ends with 02cdfc6febebcd1d175859a6ea84018fe47c345f5e44fffddf97a492112f
        $proofOfKnowledgeOfEncryptionCoins = $ballot['proofOfKnowledgeOfEncryptionCoins'];
        $content .= self::getCollectionHexLength4Bytes($proofOfKnowledgeOfEncryptionCoins);
        foreach ($proofOfKnowledgeOfEncryptionCoins as $proofOfKnowledgeOfEncryptionCoin) {
            $cBytes = '00'.self::bcdechex($proofOfKnowledgeOfEncryptionCoin['c']);
            $fBytes = '00'.self::bcdechex($proofOfKnowledgeOfEncryptionCoin['f']);

            $content .= self::getBytesHexLength4Bytes($cBytes).$cBytes;
            $content .= self::getBytesHexLength4Bytes($fBytes).$fBytes;
        }

        $proofOfKnowledgeOfPrivateCredential = $ballot['proofOfKnowledgeOfPrivateCredential'];
        $cBytes = '00'.self::bcdechex($proofOfKnowledgeOfPrivateCredential['c']);
        $fBytes = self::bcdechex($proofOfKnowledgeOfPrivateCredential['f']);

        $content .= self::getBytesHexLength4Bytes($cBytes).$cBytes;
        $content .= self::getBytesHexLength4Bytes($fBytes).$fBytes;

        return $content;
    }

    private static function getBytesHexLength4Bytes(string $content): string
    {
        return self::getHexLength4Bytes((int) (strlen($content) / 2));
    }

    private static function getStringHexLength4Bytes(string $content): string
    {
        return self::getHexLength4Bytes(mb_strlen($content));
    }

    /**
     * @param mixed[] $collection
     */
    private static function getCollectionHexLength4Bytes(array $collection): string
    {
        return self::getHexLength4Bytes(count($collection));
    }

    public static function bcdechex(string $dec): string
    {
        $hex = '';
        do {
            $last = (int) bcmod($dec, '16');
            $hex = dechex($last).$hex;
            $dec = bcdiv(bcsub($dec, (string) $last), '16');
        } while ($dec > 0);

        return $hex;
    }

    private static function getHexLength4Bytes(int $length): string
    {
        $hexLength = dechex($length);

        return str_pad($hexLength, 8, '0', STR_PAD_LEFT);
    }
}
