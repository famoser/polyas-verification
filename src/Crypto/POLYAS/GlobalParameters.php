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

readonly class GlobalParameters
{
    public static function getPOLYASCommitmentGeneratorH(): string
    {
        return '0373744f99d31509eb5f8caaabc0cc3fab70e571a5db4d762020723b9cd6ada260';
    }
}
