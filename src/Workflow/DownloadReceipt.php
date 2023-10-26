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
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;

readonly class DownloadReceipt
{
    public const RECEIPT_VALID = 'RECEIPT_VALID';
    public const PDF_GENERATED = 'PDF_GENERATED';

    public function __construct(private string $verificationKeyX509Hex, private string $polyasElection)
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

        if (!$this->generatePdf($fingerprint, $signature, $this->polyasElection, null, $pdf)) {
            $failedCheck = self::PDF_GENERATED;

            return false;
        }

        return true;
    }

    private function generatePdf(string $fingerprint, string $signature, string $polyasElection, ?string $ballotVoterId, string &$pdf = null): bool
    {
        try {
            $document = new LinearDocument();
            $flow = new Flow(Flow::DIRECTION_COLUMN);

            self::addIntroduction($flow);
            self::addFingerprintAndSignature($flow, $fingerprint, $signature);
            self::addMeta($flow, $polyasElection, $ballotVoterId);

            $document->add($flow);
            $pdf = $document->save();

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    private function addIntroduction(Flow $flow): void
    {
        $normalFont = Font::createFromDefault();
        $normalText = new TextStyle($normalFont);

        $headerFont = Font::createFromDefault(Font\FontFamily::Helvetica, Font\FontWeight::Bold);
        $headerText = new TextStyle($headerFont, $normalText->getFontSize() * 1.6 * 2);

        $paragraph = new Paragraph();
        $paragraph->add($headerText, 'Rezept');
        $flow->addContent($paragraph);

        $contentOfReceipt = 'Dieses Rezept enthält eine Referenz (ein "Fingerprint") einer verschlüsselten Stimme, sowie eine vom POLYAS Server ausgestellte gültige Signatur davon.';
        $purposeOfReceipt = 'Mit dem Rezept kann überprüft werden, ob die Stimme wirklich ausgezählt wurde.';
        $introduction = $contentOfReceipt.' '.$purposeOfReceipt;

        $howToVerify = 'Stellen Sie das Rezept von Ihnen vertrauten Auditor:innen zu. Die Auditor:innen können damit überprüfen, ob die referenzierte Stimme im Wahlresultat enthalten ist, und somit auch wirklich ausgezählt wurde.';
        $verificationIsPrivate = 'Durch die Verifizierung bleibt das Stimmgeheimnis gewahrt: Nur mit dem Rezept ist es nicht möglich, die Stimme wieder zu entschlüsseln (auch nicht für die Auditor:innen).';
        $howTo = $howToVerify.' '.$verificationIsPrivate;

        foreach ([$introduction, $howTo] as $text) {
            $paragraph = new Paragraph();
            $paragraph->add($normalText, $text);

            $contentBlock = new ContentBlock($paragraph);
            $contentBlock->setMargin([0, $normalText->getLineHeight() * 1.6, 0, 0]);

            $flow->add($contentBlock);
        }
    }

    private function addFingerprintAndSignature(Flow $flow, string $fingerprint, string $signature): void
    {
        $codeFont = Font::createFromDefault(Font\FontFamily::Courier);
        $normalText = new TextStyle($codeFont);

        $paragraph = new Paragraph();
        $paragraph->add($normalText, $fingerprint.$signature);

        $contentBlock = new ContentBlock($paragraph);
        $contentBlock->setMargin([0, $normalText->getLineHeight() * 1.6 * 4, 0, 0]);

        $flow->add($contentBlock);
    }

    private function addMeta(Flow $flow, string $polyasElection, ?string $ballotVoterId): void
    {
        $codeFont = Font::createFromDefault(Font\FontFamily::Courier);
        $normalText = new TextStyle($codeFont, 3.8 / 1.6);

        $paragraph = new Paragraph();
        $paragraph->add($normalText, 'Election: '.$polyasElection);
        if ($ballotVoterId) {
            $paragraph->add($normalText, "\nBallot Voter ID: ".$ballotVoterId);
        }

        $contentBlock = new ContentBlock($paragraph);
        $contentBlock->setMargin([0, $normalText->getLineHeight() * 1.6 * 2, 0, 0]);

        $flow->add($contentBlock);
    }
}
