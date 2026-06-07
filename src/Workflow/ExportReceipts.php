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

use Famoser\PolyasVerification\PDFGenerator;
use Famoser\PolyasVerification\Storage;

readonly class ExportReceipts
{
    public const string PDF_GENERATION_FAILED = 'PDF_GENERATION_FAILED';

    public function __construct(private string $polyasElection)
    {
    }

    /**
     * @param string[] $pdfs
     *
     * @phpstan-assert-if-false string $error
     * @phpstan-assert-if-true string[] $pdfs
     */
    public function exportAll(?array &$pdfs = null, ?string &$error = null): bool
    {
        $pdfs = [];

        $receipts = Storage::getReceipts($this->polyasElection);
        $generator = new PDFGenerator();
        foreach ($receipts as $index => $receipt) {
            if (!$generator->generate($receipt, $receipt['electionId'], $pdf)) {
                $error = self::PDF_GENERATION_FAILED;

                return false;
            }

            $pdfs["receipt" . $index . ".pdf"] = $pdf;
        }

        return true;
    }
}
