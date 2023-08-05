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

use Famoser\PolyasVerification\Crypto\PEM\Decoder;

readonly class Receipt
{
    public const RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE = 'RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE';
    public const SIGNATURE_VALID = 'SIGNATURE_VALID';
    public const FINGERPRINT_REGISTERED = 'FINGERPRINT_REGISTERED';

    public function verify(string $path, string &$failedCheck = null): bool
    {
        if (!self::getFingerprintAndSignature($path, $fingerprint, $signature)) {
            $failedCheck = self::RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE;

            return false;
        }

        // TODO check signature valid

        // TODO check fingerprint registered at POLYAS

        return false;
    }

    private function getFingerprintAndSignature(string $path, ?string &$fingerprint, ?string &$signature): bool
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
