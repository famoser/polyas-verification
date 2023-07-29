<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\POLYAS\Utils;

class PlaintextEncodingException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
