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

class ExportReceipts
{
    public const PDF_GENERATION_FAILED = 'PDF_GENERATION_FAILED';

    public function __construct(private string $polyasElection)
    {
    }

    /**
     * @param string[] $pdfs
     */
    public function exportAll(?array &$pdfs = null, ?string &$error = null): bool
    {
        $pdfs = [];

        $receipts = Storage::getReceipts($this->polyasElection);
        foreach ($receipts as $receipt) {
            if (!PDFGenerator::generate($receipt, $receipt['electionId'], $pdf)) {
                $error = self::PDF_GENERATION_FAILED;

                return false;
            }

            $pdfs[] = $pdf;
        }

        return true;
    }
}
