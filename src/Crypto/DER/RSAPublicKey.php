<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\DER;

readonly class RSAPublicKey
{
    public function __construct(private string $n, private string $e)
    {
    }

    public function getN(): string
    {
        return $this->n;
    }

    public function getE(): string
    {
        return $this->e;
    }
}
