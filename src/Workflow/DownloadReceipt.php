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

use Famoser\PolyasVerification\Crypto\PEM\Encoder;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigestSignature;
use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;

readonly class DownloadReceipt
{
    public const RECEIPT_VALID = 'RECEIPT_VALID';
    public const PDF_GENERATED = 'PDF_GENERATED';

    public function __construct(private string $verificationKeyX509Hex)
    {
    }

    /**
     * @param array{
     * 'fingerprint': string,
     * 'signature': string,
     * } $receipt
     */
    public function store(array $receipt, string &$pdf = null, string &$failedCheck = null): bool
    {
        $ballotSignature = BallotDigestSignature::createFromExport($receipt, $this->verificationKeyX509Hex);
        if (!$ballotSignature->verify()) {
            $failedCheck = self::RECEIPT_VALID;

            return false;
        }

        $fingerprint = Encoder::encodeRaw('FINGERPRINT', $receipt['fingerprint']);
        $signature = Encoder::encodeRaw('SIGNATURE', $receipt['signature']);

        if (!$this->generatePdf($fingerprint, $signature, $pdf)) {
            $failedCheck = self::PDF_GENERATED;

            return false;
        }

        return true;
    }

    /**
     * @param array{
     * 'fingerprint': string,
     * 'signature': string,
     * } $receipt
     */
    private function generatePdf(string $fingerprint, string $signature, string &$pdf = null): bool
    {
        try {
            $document = new LinearDocument();
            $flow = new Flow();

            $normalFont = Font::createFromDefault(Font\FontFamily::Courier);
            $normalText = new TextStyle($normalFont);

            $paragraph = new Paragraph();
            $paragraph->add($normalText, $fingerprint.$signature);
            $flow->addContent($paragraph);

            $document->add($flow);

            $pdf = $document->save();

            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
