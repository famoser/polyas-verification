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

/**
 * @phpstan-type ValidReceipt array{
 *     fingerprint: string,
 *     signature: string,
 * }
 */
readonly class VerifyReceipt
{
    public const string RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE = 'RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE';
    public const string SIGNATURE_VALID = 'SIGNATURE_VALID';

    public function __construct(private string $verificationKeyX509Hex)
    {
    }

    /**
     * @param ValidReceipt|null $validReceipt
     *
     * @phpstan-assert-if-false string $failedCheck
     * @phpstan-assert-if-true ValidReceipt $validReceipt
     */
    public function verify(string $path, ?array &$validReceipt = null, ?string &$failedCheck = null): bool
    {
        if (!self::getFingerprintAndSignature($path, $fingerprint, $signature)) {
            $failedCheck = self::RECEIPT_HAS_FINGERPRINT_AND_SIGNATURE;

            return false;
        }

        /** @var string $verificationKeyX509 */
        $verificationKeyX509 = hex2bin($this->verificationKeyX509Hex);
        $ballotSignature = new BallotDigestSignature($fingerprint, $signature, $verificationKeyX509);
        if (!$ballotSignature->verify()) {
            $failedCheck = self::SIGNATURE_VALID;

            return false;
        }

        $validReceipt = $ballotSignature->export();

        // optional: check fingerprint registered at POLYAS

        return true;
    }

    /**
     * @phpstan-assert-if-true string $fingerprint
     * @phpstan-assert-if-true string $signature
     */
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

    /**
     * @phpstan-assert-if-true string $fingerprint
     * @phpstan-assert-if-true string $signature
     */
    private function getFingerprintAndSignatureRaw(string $content, ?string &$fingerprint, ?string &$signature): bool
    {
        // \(([A-Z-0-9 a-f]+)\)( *(Tj)|')
        preg_match_all('/\(([A-Z-0-9 a-f]+)\)( *(Tj)|\')/', $content, $matches, PREG_OFFSET_CAPTURE);
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
     *
     * @phpstan-assert-if-true string $fingerprint
     * @phpstan-assert-if-true string $signature
     */
    private function parseDecodedPEM(array $payloads, ?string &$fingerprint, ?string &$signature): bool
    {
        $fingerprintRaw = false;
        $signatureRaw = false;
        foreach ($payloads as $payload) {
            switch ($payload->getLabel()) {
                case 'FINGERPRINT':
                    $fingerprintRaw = hex2bin($payload->getRawPayload());
                    break;
                case 'SIGNATURE':
                    $signatureRaw = hex2bin($payload->getRawPayload());
                    break;
            }
        }

        if (!$fingerprintRaw || !$signatureRaw) {
            return false;
        }

        $signature = $signatureRaw;
        $fingerprint = $fingerprintRaw;

        return true;
    }
}
