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

/**
 * Implements https://www.rfc-editor.org/rfc/rfc7468.
 */
class Pem
{
    private const ENCAPSULATION_BOUNDARY_MARKER = '-----';
    private const BEGIN_MARKER = self::ENCAPSULATION_BOUNDARY_MARKER.'BEGIN ';
    private const END_MARKER = self::ENCAPSULATION_BOUNDARY_MARKER.'END ';

    /**
     * @param array<array{'label': string, 'payload': string}> $payloads
     */
    public static function extractPayloads(string $pem, array &$payloads, string &$error = null): bool
    {
        // MUST handle different newline conventions
        $lines = preg_split('/\R+/', $pem, 0, PREG_SPLIT_NO_EMPTY);
        if (!$lines) {
            return true;
        }

        $activeMarker = null;
        $activeSection = '';
        for ($i = 0; $i < count($lines); ++$i) {
            $line = $lines[$i];
            if (str_starts_with($line, self::BEGIN_MARKER)) {
                if (!str_ends_with($line, self::ENCAPSULATION_BOUNDARY_MARKER)) {
                    $error = 'line '.$i.': Start marker does not end with '.self::ENCAPSULATION_BOUNDARY_MARKER;

                    return false;
                }

                $activeMarker = substr($line, strlen(self::BEGIN_MARKER));
                continue;
            }

            if (str_starts_with($line, self::END_MARKER) && $activeMarker) {
                // Generators MUST put the same label on the "-----END " line (post-encapsulation boundary) as the corresponding "-----BEGIN " line.
                $expectedEnd = self::END_MARKER.$activeMarker;
                if ($expectedEnd !== $line) {
                    $error = 'line '.$i.': End marker is not exactly '.$expectedEnd;

                    return false;
                }

                $label = substr($activeMarker, 0, strlen($activeMarker) - strlen(self::ENCAPSULATION_BOUNDARY_MARKER));
                $payload = base64_decode($activeSection);
                if (!$payload) {
                    $error = 'Cannot base64 decode the section with label '.$label;

                    return false;
                }

                $payloads[] = ['label' => $label, 'payload' => $payload];

                $activeMarker = null;
                $activeSection = '';
                continue;
            }

            if ($activeMarker) {
                // Most extant parsers ignore blanks at the ends of lines.
                $activeSection .= trim($line);
            }

            // Data before the encapsulation boundaries are permitted, and parsers MUST NOT malfunction
        }

        if ($activeMarker) {
            $error = 'Expected end marker '.self::END_MARKER.$activeMarker.' not found.';

            return false;
        }

        return true;
    }
}
