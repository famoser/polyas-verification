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
     *     'encryptedChoice': array{'ciphertexts': array{array{'x': string, 'y': string}}},
     *     'zkp': array{array{'c': numeric-string, 'f': numeric-string}},
     *     'publicLabel': string,
     *     'reference': string,
     *     'signature': array{'c': numeric-string, 'f': numeric-string},
     *  } $ballot
     */
    public function __construct(private array $ballot)
    {
    }

    public function createFingerprint(): string
    {
        $digestHex = $this->createNormalizedHex();
        /** @var string $digest */
        $digest = \hex2bin($digestHex);

        return hash('sha256', $digest, true);
    }

    public function createNormalizedHex(): string
    {
        $ciphertexts = $this->ballot['encryptedChoice']['ciphertexts'];
        $content = Utils\Serialization::getCollectionHexLength4Bytes($ciphertexts);
        foreach ($ciphertexts as $ciphertext) {
            $content .= Utils\Serialization::getBytesHexLength4Bytes($ciphertext['x']) . $ciphertext['x'];
            $content .= Utils\Serialization::getBytesHexLength4Bytes($ciphertext['y']) . $ciphertext['y'];
        }

        $content .= Utils\Serialization::getStringHexWithLength($this->ballot['publicLabel']);
        $content .= Utils\Serialization::getStringHexWithLength($this->ballot['reference']);

        $zkp = $this->ballot['zkp'];
        $content .= Utils\Serialization::getCollectionHexLength4Bytes($zkp);
        foreach ($zkp as $entry) {
            $content .= Utils\Serialization::getNumericStringHexWithLength($entry['c']);
            $content .= Utils\Serialization::getNumericStringHexWithLength($entry['f']);
        }

        $signature = $this->ballot['signature'];
        $content .= Utils\Serialization::getNumericStringHexWithLength($signature['c']);
        $content .= Utils\Serialization::getNumericStringHexWithLength($signature['f']);

        return $content;
    }
}
