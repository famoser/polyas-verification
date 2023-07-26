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

use Famoser\PolyasVerification\Crypto\PEM;
use Famoser\PolyasVerification\Crypto\RSA\Signature;

class BallotEntry
{
    /**
     * @param array{
     *     'publicLabel': string,
     *     'publicCredential': string,
     *     'voterId': string,
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
     *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
     *      }
     *     } $ballotEntry
     */
    public static function verifySignature(array $ballotEntry, string $signatureHex, string $verificationKeyHexX509): void
    {
        $data = self::createDigestBin($ballotEntry);

        /** @var string $verificationKeyBin */
        $verificationKeyBin = hex2bin($verificationKeyHexX509);
        $publicKeyPem = PEM\Encoder::encode('PUBLIC KEY', $verificationKeyBin);

        $signature = hex2bin($signatureHex);
        var_dump(strlen($signature));

        Signature::verifySHA256($data, $signature, $publicKeyPem);
    }

    /**
     * @param array{
     *     'publicLabel': string,
     *     'publicCredential': string,
     *     'voterId': string,
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
     *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
     *      }
     *     } $ballotEntry
     */
    public static function createFingerprint(array $ballotEntry): string
    {
        $digest = self::createDigestBin($ballotEntry);

        return hash('sha256', $digest);
    }

    /**
     * @param array{
     *     'publicLabel': string,
     *     'publicCredential': string,
     *     'voterId': string,
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
     *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
     *      }
     *     } $ballotEntry
     */
    public static function createDigestBin(array $ballotEntry): string
    {
        $digestHex = self::createDigestHex($ballotEntry);
        $digest = hex2bin($digestHex);
        if (!$digest) {
            throw new \RuntimeException('Cannot transform digest hex to binary');
        }

        return $digest;
    }

    /**
     * @param array{
     *     'publicLabel': string,
     *     'publicCredential': string,
     *     'voterId': string,
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
        $voterId = $ballotEntry['voterId'];

        $content = self::getStringHexWithLength($publicLabel);
        $content .= self::getBytesHexLength4Bytes($publicCredential).$publicCredential;
        $content .= self::getStringHexWithLength($voterId);

        $ballot = $ballotEntry['ballot'];
        $ciphertexts = $ballot['encryptedChoice']['ciphertexts'];
        $content .= self::getCollectionHexLength4Bytes($ciphertexts);
        foreach ($ciphertexts as $ciphertext) {
            $content .= self::getBytesHexLength4Bytes($ciphertext['x']).$ciphertext['x'];
            $content .= self::getBytesHexLength4Bytes($ciphertext['y']).$ciphertext['y'];
        }

        $proofOfKnowledgeOfEncryptionCoins = $ballot['proofOfKnowledgeOfEncryptionCoins'];
        $content .= self::getCollectionHexLength4Bytes($proofOfKnowledgeOfEncryptionCoins);
        foreach ($proofOfKnowledgeOfEncryptionCoins as $proofOfKnowledgeOfEncryptionCoin) {
            $cBytes = self::bcdechex($proofOfKnowledgeOfEncryptionCoin['c']);
            $fBytes = self::bcdechex($proofOfKnowledgeOfEncryptionCoin['f']);

            $content .= self::getBytesHexLength4Bytes($cBytes).$cBytes;
            $content .= self::getBytesHexLength4Bytes($fBytes).$fBytes;
        }

        $proofOfKnowledgeOfPrivateCredential = $ballot['proofOfKnowledgeOfPrivateCredential'];
        $cBytes = self::bcdechex($proofOfKnowledgeOfPrivateCredential['c']);
        $fBytes = self::bcdechex($proofOfKnowledgeOfPrivateCredential['f']);

        $content .= self::getBytesHexLength4Bytes($cBytes).$cBytes;
        $content .= self::getBytesHexLength4Bytes($fBytes).$fBytes;

        return $content;
    }

    private static function getBytesHexLength4Bytes(string $content): string
    {
        return self::getHexLength4Bytes((int) (strlen($content) / 2));
    }

    private static function getStringHexWithLength(string $content): string
    {
        $content = bin2hex($content);

        return self::getHexLength4Bytes((int) (strlen($content) / 2)).$content;
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

        if (1 === strlen($hex) % 2) {
            $hex = '0'.$hex;
        }

        // TODO verify this is actually what happens
        if ($hex[0] > '7') {
            $hex = '00'.$hex;
        }

        return $hex;
    }

    private static function getHexLength4Bytes(int $length): string
    {
        $hexLength = dechex($length);

        return str_pad($hexLength, 8, '0', STR_PAD_LEFT);
    }
}
