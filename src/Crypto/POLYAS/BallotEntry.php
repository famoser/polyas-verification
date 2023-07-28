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

readonly class BallotEntry
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
     *     } $content
     */
    public function __construct(private array $content)
    {
    }

    public function createFingerprint(): string
    {
        $digest = self::createDigestBin();

        return hash('sha256', $digest);
    }

    public function createDigestBin(): string
    {
        $digestHex = self::createDigestHex();
        $digest = hex2bin($digestHex);
        if (!$digest) {
            throw new \RuntimeException('Cannot transform digest hex to binary');
        }

        return $digest;
    }

    public function createDigestHex(): string
    {
        $publicLabel = $this->content['publicLabel'];
        $publicCredential = $this->content['publicCredential'];
        $voterId = $this->content['voterId'];

        $content = Utils\Serialization::getStringHexWithLength($publicLabel);
        $content .= Utils\Serialization::getBytesHexLength4Bytes($publicCredential).$publicCredential;
        $content .= Utils\Serialization::getStringHexWithLength($voterId);

        $ballot = $this->content['ballot'];
        $ciphertexts = $ballot['encryptedChoice']['ciphertexts'];
        $content .= Utils\Serialization::getCollectionHexLength4Bytes($ciphertexts);
        foreach ($ciphertexts as $ciphertext) {
            $content .= Utils\Serialization::getBytesHexLength4Bytes($ciphertext['x']).$ciphertext['x'];
            $content .= Utils\Serialization::getBytesHexLength4Bytes($ciphertext['y']).$ciphertext['y'];
        }

        $proofOfKnowledgeOfEncryptionCoins = $ballot['proofOfKnowledgeOfEncryptionCoins'];
        $content .= Utils\Serialization::getCollectionHexLength4Bytes($proofOfKnowledgeOfEncryptionCoins);
        foreach ($proofOfKnowledgeOfEncryptionCoins as $proofOfKnowledgeOfEncryptionCoin) {
            $cBytes = Utils\Serialization::bcdechexFixed($proofOfKnowledgeOfEncryptionCoin['c']);
            $fBytes = Utils\Serialization::bcdechexFixed($proofOfKnowledgeOfEncryptionCoin['f']);

            $content .= Utils\Serialization::getBytesHexLength4Bytes($cBytes).$cBytes;
            $content .= Utils\Serialization::getBytesHexLength4Bytes($fBytes).$fBytes;
        }

        $proofOfKnowledgeOfPrivateCredential = $ballot['proofOfKnowledgeOfPrivateCredential'];
        $cBytes = Utils\Serialization::bcdechexFixed($proofOfKnowledgeOfPrivateCredential['c']);
        $fBytes = Utils\Serialization::bcdechexFixed($proofOfKnowledgeOfPrivateCredential['f']);

        $content .= Utils\Serialization::getBytesHexLength4Bytes($cBytes).$cBytes;
        $content .= Utils\Serialization::getBytesHexLength4Bytes($fBytes).$fBytes;

        return $content;
    }
}
