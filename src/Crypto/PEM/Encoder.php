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
class Encoder
{
    private const ENCAPSULATION_BOUNDARY_MARKER = '-----';
    private const BEGIN_MARKER = self::ENCAPSULATION_BOUNDARY_MARKER.'BEGIN ';
    private const END_MARKER = self::ENCAPSULATION_BOUNDARY_MARKER.'END ';

    public static function encode(string $label, string $payload): string
    {
        $encodedPayload = base64_encode($payload);

        return self::encodeRaw($label, $encodedPayload);
    }

    public static function encodeRaw(string $label, string $encodedPayload): string
    {
        $begin = self::BEGIN_MARKER.$label.self::ENCAPSULATION_BOUNDARY_MARKER;
        $end = self::END_MARKER.$label.self::ENCAPSULATION_BOUNDARY_MARKER;
        $content = chunk_split($encodedPayload, 64, PHP_EOL);

        return $begin.PHP_EOL.$content.$end.PHP_EOL;
    }
}
