<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\POLYAS;

readonly class KeyDerivation
{
    private const SHA512_LENGTH_IN_BYTES = 64;

    public function __construct(private string $keyDerivationKey, private int $length, private string $label, private string $context)
    {
    }

    public function derive(): string
    {
        $result = '';
        $iterations = ceil($this->length / self::SHA512_LENGTH_IN_BYTES);
        for ($i = 0; $i < $iterations; ++$i) {
            $result .= $this->iterate($i);
        }

        return substr($result, 0, $this->length);
    }

    private function iterate(int $iteration): string
    {
        $data = pack('N', $iteration);
        $data .= $this->label;
        $data .= hex2bin('00');
        $data .= $this->context;
        $data .= pack('N', $this->length);

        return hash_hmac('sha512', $data, $this->keyDerivationKey, true);
    }
}
