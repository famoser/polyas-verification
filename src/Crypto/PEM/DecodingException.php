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

class DecodingException extends \RuntimeException
{
    public function __construct(string $message, int $line = null)
    {
        if (null !== $line) {
            $message = 'line '.$line.': '.$message;
        }
        parent::__construct($message);
    }
}
