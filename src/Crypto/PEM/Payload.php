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

class Payload
{
    private string $label;
    private string $payload;

    public function __construct(string $label, string $payload)
    {
        $this->label = $label;
        $this->payload = $payload;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getPayload(): string
    {
        return base64_decode($this->payload);
    }

    public function getRawPayload(): string
    {
        return $this->payload;
    }
}
