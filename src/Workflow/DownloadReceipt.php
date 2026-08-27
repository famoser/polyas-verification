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

use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigestSignature;
use Famoser\PolyasVerification\PDFGenerator;

readonly class DownloadReceipt
{
    public const string RECEIPT_VALID = 'RECEIPT_VALID';
    public const string PDF_GENERATED = 'PDF_GENERATED';

    public function __construct(private string $verificationKeyX509Hex, private string $polyasElection)
    {
    }

    /**
     * @param array{
     * 'fingerprint': string,
     *  'signature': string,
     * } $receipt
     *
     * @phpstan-assert-if-false string $failedCheck
     * @phpstan-assert-if-true string $pdf
     */
    public function store(array $receipt, ?string &$pdf = null, ?string &$failedCheck = null): bool
    {
        $ballotSignature = BallotDigestSignature::createFromExport($receipt, $this->verificationKeyX509Hex);
        if (!$ballotSignature->verify()) {
            $failedCheck = self::RECEIPT_VALID;

            return false;
        }

        $generator = new PDFGenerator();
        if (!$generator->generate($receipt, $this->polyasElection, $pdf)) {
            $failedCheck = self::PDF_GENERATED;

            return false;
        }

        return true;
    }
}
