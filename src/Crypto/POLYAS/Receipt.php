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

use Famoser\PolyasVerification\Crypto\PEM\Decoder;

class Receipt
{
    public const RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE = 'RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE';
    public const SIGNATURE_VALID = 'SIGNATURE_VALID';
    public const FINGERPRINT_REGISTERED = 'FINGERPRINT_REGISTERED';

    /**
     * @return bool[]
     */
    public static function verify(string $path): array
    {
        $verificationResult = [
            self::RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE => false,
            self::SIGNATURE_VALID => false,
            self::FINGERPRINT_REGISTERED => false,
        ];

        if (!self::getFingerprintAndSignature($path, $fingerprint, $signature)) {
            return $verificationResult;
        }
        $verificationResult[self::RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE] = true;

        // TODO check signature valid

        // TODO check fingerprint registered at POLYAS

        return $verificationResult;
    }

    private static function getFingerprintAndSignature(string $path, ?string &$fingerprint, ?string &$signature): bool
    {
        $content = file_get_contents($path);
        if (!$content) {
            return false;
        }

        $payloads = Decoder::decode($content);
        if (2 !== count($payloads)) {
            return false;
        }

        foreach ($payloads as $payload) {
            switch ($payload->getLabel()) {
                case 'FINGERPRINT':
                    $fingerprint = $payload->getPayload();
                    break;
                case 'SIGNATURE':
                    $signature = $payload->getPayload();
                    break;
            }
        }

        return $fingerprint && $signature;
    }
}
