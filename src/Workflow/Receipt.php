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
use Famoser\PolyasVerification\Crypto\PEM\Payload;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigestSignature;

readonly class Receipt
{
    public const RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE = 'RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE';
    public const SIGNATURE_VALID = 'SIGNATURE_VALID';
    public const FINGERPRINT_REGISTERED = 'FINGERPRINT_REGISTERED';

    public function __construct(private string $verificationKeyX509)
    {
    }

    public function verify(string $path, string &$failedCheck = null): bool
    {
        if (!self::getFingerprintAndSignature($path, $fingerprint, $signature)) {
            $failedCheck = self::RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE;

            return false;
        }

        $ballotSignature = new BallotDigestSignature($fingerprint, $signature, $this->verificationKeyX509);
        if (!$ballotSignature->verify()) {
            $failedCheck = self::SIGNATURE_VALID;

            return false;
        }

        // TODO check fingerprint registered at POLYAS

        return false;
    }

    public function getFingerprintAndSignature(string $path, ?string &$fingerprint, ?string &$signature): bool
    {
        $content = file_get_contents($path);
        if (!$content) {
            return false;
        }

        $payloads = Decoder::decode($content);
        if (2 !== count($payloads)) {
            return $this->getFingerprintAndSignatureRaw($content, $fingerprint, $signature);
        }

        return $this->parseDecodedPEM($payloads, $fingerprint, $signature);
    }

    private function getFingerprintAndSignatureRaw(string $content, ?string &$fingerprint, ?string &$signature): bool
    {
        preg_match_all('/\((.+)\) Tj/', $content, $matches, PREG_OFFSET_CAPTURE);
        $extractedLines = [];
        foreach ($matches[1] as $match) {
            $extractedLines[] = $match[0];
        }

        $extractedText = implode("\n", $extractedLines);
        $payloads = Decoder::decode($extractedText);
        if (2 !== count($payloads)) {
            return false;
        }

        return $this->parseDecodedPEM($payloads, $fingerprint, $signature);
    }

    /**
     * @param Payload[] $payloads
     */
    private function parseDecodedPEM(array $payloads, ?string &$fingerprint, ?string &$signature): bool
    {
        foreach ($payloads as $payload) {
            switch ($payload->getLabel()) {
                case 'FINGERPRINT':
                    $fingerprint = hex2bin($payload->getRawPayload());
                    break;
                case 'SIGNATURE':
                    $signature = hex2bin($payload->getRawPayload());
                    break;
            }
        }

        return $fingerprint && $signature;
    }
}
