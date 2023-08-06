<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\PEM;

/**
 * Implements https://www.rfc-editor.org/rfc/rfc7468.
 * Citations from that RFC are prefix with RFC:.
 */
class Decoder
{
    private const ENCAPSULATION_BOUNDARY_MARKER = '-----';
    private const BEGIN_MARKER = self::ENCAPSULATION_BOUNDARY_MARKER.'BEGIN ';
    private const END_MARKER = self::ENCAPSULATION_BOUNDARY_MARKER.'END ';

    /**
     * @return Payload[]
     */
    public static function decode(string $pem): array
    {
        // RFC: MUST handle different newline conventions
        $lines = preg_split('/\R+/', $pem, 0, PREG_SPLIT_NO_EMPTY);
        if (!$lines) {
            return [];
        }

        $payloads = [];
        $activeMarker = null;
        $activeSection = '';
        for ($i = 0; $i < count($lines); ++$i) {
            $line = $lines[$i];
            if (str_starts_with($line, self::BEGIN_MARKER)) {
                if (!str_ends_with($line, self::ENCAPSULATION_BOUNDARY_MARKER)) {
                    throw new DecodingException('Start marker does not end with '.self::ENCAPSULATION_BOUNDARY_MARKER, $i);
                }

                $activeMarker = substr($line, strlen(self::BEGIN_MARKER));
                continue;
            }

            if (str_starts_with($line, self::END_MARKER) && $activeMarker) {
                // RFC: Generators MUST put the same label on the "-----END " line (post-encapsulation boundary) as the corresponding "-----BEGIN " line.
                $expectedEnd = self::END_MARKER.$activeMarker;
                if ($expectedEnd !== $line) {
                    throw new DecodingException('End marker is not exactly '.$expectedEnd, $i);
                }

                $label = substr($activeMarker, 0, strlen($activeMarker) - strlen(self::ENCAPSULATION_BOUNDARY_MARKER));
                $payloads[] = new Payload($label, $activeSection);

                $activeMarker = null;
                $activeSection = '';
                continue;
            }

            if ($activeMarker) {
                // RFC: Most extant parsers ignore blanks at the ends of lines.
                $activeSection .= trim($line);
            }

            // RFC: Data before the encapsulation boundaries are permitted, and parsers MUST NOT malfunction
        }

        if ($activeMarker) {
            throw new DecodingException('Expected end marker '.self::END_MARKER.$activeMarker.' not found.');
        }

        return $payloads;
    }
}
