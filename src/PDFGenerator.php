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

use Famoser\PolyasVerification\Crypto\PEM\Encoder;
use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LinearDocument;
use PdfGenerator\Frontend\Resource\Font;

class PDFGenerator
{
    /**
     * @param array{
     *  'fingerprint': string,
     *   'signature': string,
     *   'ballotVoterId': string,
     *  } $receipt
     */
    public static function generate(array $receipt, ?string $polyasElection, ?string &$pdf = null): bool
    {
        $fingerprint = Encoder::encodeRaw('FINGERPRINT', $receipt['fingerprint']);
        $signature = Encoder::encodeRaw('SIGNATURE', $receipt['signature']);
        $ballotVoterId = $receipt['ballotVoterId'];

        try {
            $document = new LinearDocument();
            $flow = new Flow(Flow::DIRECTION_COLUMN);

            self::addIntroduction($flow);
            self::addFingerprintAndSignature($flow, $fingerprint, $signature);
            self::addMeta($flow, $ballotVoterId, $polyasElection);

            $document->add($flow);
            $pdf = $document->save();

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    private static function addIntroduction(Flow $flow): void
    {
        $normalFont = Font::createFromDefault();
        $normalText = new TextStyle($normalFont);

        $headerFont = Font::createFromDefault(Font\FontFamily::Helvetica, Font\FontWeight::Bold);
        $headerText = new TextStyle($headerFont, $normalText->getFontSize() * 1.6 * 2);

        $paragraph = new Paragraph();
        $paragraph->add($headerText, 'Wahl-Quittung');
        $flow->addContent($paragraph);

        $contentOfReceipt = 'Diese Wahl-Quittung enthält eine Referenz (ein "Fingerprint") einer verschlüsselten Stimme, sowie eine vom Wahl-Server ausgestellte gültige Signatur davon.';

        $howToVerify = 'Stellen Sie die Wahl-Quittung Auditor:innen zu, denen Sie vertrauen. Die Auditor:innen können damit überprüfen, ob die referenzierte Stimme im Wahlresultat enthalten ist, und somit auch wirklich ausgezählt wurde.';
        $verificationIsPrivate = 'Durch die Verifizierung bleibt das Stimmgeheimnis gewahrt: Nur mit der Wahl-Quittung ist es nicht möglich, die Stimme wieder zu entschlüsseln (auch nicht für die Auditor:innen).';
        $howTo = $howToVerify.' '.$verificationIsPrivate;

        foreach ([$contentOfReceipt, $howTo] as $text) {
            $paragraph = new Paragraph();
            $paragraph->add($normalText, $text);

            $contentBlock = new ContentBlock($paragraph);
            $contentBlock->setMargin([0, $normalText->getLineHeight() * 1.6, 0, 0]);

            $flow->add($contentBlock);
        }
    }

    private static function addFingerprintAndSignature(Flow $flow, string $fingerprint, string $signature): void
    {
        $codeFont = Font::createFromDefault(Font\FontFamily::Courier);
        $normalText = new TextStyle($codeFont);

        $paragraph = new Paragraph();
        $paragraph->add($normalText, $fingerprint.$signature);

        $contentBlock = new ContentBlock($paragraph);
        $contentBlock->setMargin([0, $normalText->getLineHeight() * 1.6 * 4, 0, 0]);

        $flow->add($contentBlock);
    }

    private static function addMeta(Flow $flow, string $ballotVoterId, ?string $polyasElection): void
    {
        if (!$ballotVoterId && !$polyasElection) {
            return;
        }

        $codeFont = Font::createFromDefault(Font\FontFamily::Courier);
        $normalText = new TextStyle($codeFont, 3.8 / 1.6);

        $paragraph = new Paragraph();
        if ($ballotVoterId) {
            $paragraph->add($normalText, 'Anonymisierte Wahl-ID: '.$ballotVoterId."\n");
        }
        if ($polyasElection) {
            $paragraph->add($normalText, 'Wahl: '.$polyasElection);
        }

        $contentBlock = new ContentBlock($paragraph);
        $contentBlock->setMargin([0, $normalText->getLineHeight() * 1.6 * 2, 0, 0]);

        $flow->add($contentBlock);
    }
}
