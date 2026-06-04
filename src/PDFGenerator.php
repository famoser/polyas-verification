<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification;

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Document;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PolyasVerification\Crypto\PEM\Encoder;

readonly class PDFGenerator
{
    private TextStyle $normalText;
    private TextStyle $codeText;
    private TextStyle $headerText;

    private float $normalFontSize;
    private float $headerFontSize;
    private float $metaFontSize;

    public function __construct()
    {
        $normalFont = Font::createFromDefault();
        $this->normalText = new TextStyle($normalFont);
        $this->normalFontSize = 8.0;

        $headerFont = Font::createFromDefault(Font\FontFamily::Helvetica, Font\FontWeight::Bold);
        $this->headerText = new TextStyle($headerFont);
        $this->headerFontSize = $this->normalFontSize * 1.6 * 2;

        $codeFont = Font::createFromDefault(Font\FontFamily::Courier);
        $this->codeText =  new TextStyle($codeFont);

        $this->metaFontSize = $this->normalFontSize / 1.6;
    }
    /**
     * @param array{
     *  'fingerprint': string,
     *   'signature': string,
     *   'ballotVoterId': string,
     *  } $receipt
     *
     * @phpstan-assert-if-true string $pdf
     */
    public function generate(array $receipt, ?string $polyasElection, ?string &$pdf = null): bool
    {
        $fingerprint = Encoder::encodeRaw('FINGERPRINT', $receipt['fingerprint']);
        $signature = Encoder::encodeRaw('SIGNATURE', $receipt['signature']);
        $ballotVoterId = $receipt['ballotVoterId'];

        try {
            $document = new Document();
            $flow = new Flow(FlowDirection::COLUMN);

            $this->addIntroduction($flow);
            $this->addFingerprintAndSignature($flow, $fingerprint, $signature);
            $this->addMeta($flow, $ballotVoterId, $polyasElection);

            $document->add($flow);
            $pdf = $document->save();

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    private function addIntroduction(Flow $flow): void
    {
        $paragraph = new Text();
        $paragraph->addSpan('Wahl-Quittung', $this->headerText, $this->headerFontSize);
        $flow->add($paragraph);

        $contentOfReceipt = 'Diese Wahl-Quittung enthält eine Referenz (ein "Fingerprint") einer verschlüsselten Stimme, sowie eine vom Wahl-Server ausgestellte gültige Signatur davon.';

        $howToVerify = 'Stellen Sie die Wahl-Quittung Auditor:innen zu, denen Sie vertrauen. Die Auditor:innen können damit überprüfen, ob die referenzierte Stimme im Wahlresultat enthalten ist, und somit auch wirklich ausgezählt wurde.';
        $verificationIsPrivate = 'Durch die Verifizierung bleibt das Stimmgeheimnis gewahrt: Nur mit der Wahl-Quittung ist es nicht möglich, die Stimme wieder zu entschlüsseln (auch nicht für die Auditor:innen).';
        $howTo = $howToVerify . ' ' . $verificationIsPrivate;

        foreach ([$contentOfReceipt, $howTo] as $text) {
            $paragraph = new Text();
            $paragraph->addSpan($text, $this->normalText, $this->normalFontSize);
            $paragraph->setMargin([0, $this->normalFontSize * 1.6, 0, 0]);

            $flow->add($paragraph);
        }
    }

    private function addFingerprintAndSignature(Flow $flow, string $fingerprint, string $signature): void
    {
        $paragraph = new Text();
        $paragraph->addSpan($fingerprint . $signature, $this->codeText, $this->normalFontSize);
        $paragraph->setMargin([0, $this->normalFontSize * 1.6 * 4, 0, 0]);

        $flow->add($paragraph);
    }

    private function addMeta(Flow $flow, string $ballotVoterId, ?string $polyasElection): void
    {
        if (!$ballotVoterId && !$polyasElection) {
            return;
        }

        $paragraph = new Text();
        if ($ballotVoterId) {
            $paragraph->addSpan('Anonymisierte Wahl-ID: ' . $ballotVoterId . "\n", $this->codeText, $this->metaFontSize);
        }
        if ($polyasElection) {
            $paragraph->addSpan('Wahl: ' . $polyasElection, $this->codeText, $this->metaFontSize);
        }
        $paragraph->setMargin([0, $this->normalFontSize * 2, 0, 0]);

        $flow->add($paragraph);
    }
}
