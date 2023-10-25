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

readonly class BallotDigest
{
    /**
     * @param array{
     *     'publicCredential': string,
     *     'ballot': array{
     *          'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *          'proofOfKnowledgeOfEncryptionCoins': array{array{'c': string, 'f': string}},
     *          'proofOfKnowledgeOfPrivateCredential': array{'c': string, 'f': string},
     *      }
     *     } $content
     */
    public function __construct(private array $content, private string $publicLabel, private string $ballotVoterId)
    {
    }

    public function createFingerprint(): string
    {
        $digestHex = BallotDigest::createDigestHex();
        /** @var string $digest */
        $digest = \hex2bin($digestHex);

        return hash('sha256', $digest, true);
    }

    public function createDigestHex(): string
    {
        $publicLabel = $this->publicLabel;
        $publicCredential = $this->content['publicCredential'];
        $ballotVoterId = $this->ballotVoterId;

        $content = Utils\Serialization::getStringHexWithLength($publicLabel);
        $content .= Utils\Serialization::getBytesHexLength4Bytes($publicCredential).$publicCredential;
        $content .= Utils\Serialization::getStringHexWithLength($ballotVoterId);

        $content .= $this->createNormalizedHex();

        return $content;
    }

    public function createNorm(): string
    {
        $normalizedHex = self::createNormalizedHex();
        /** @var string $digest */
        $digest = \hex2bin($normalizedHex);

        return hash('sha256', $digest, true);
    }

    private function createNormalizedHex(): string
    {
        $ballot = $this->content['ballot'];
        $ciphertexts = $ballot['encryptedChoice']['ciphertexts'];
        $content = Utils\Serialization::getCollectionHexLength4Bytes($ciphertexts);
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
