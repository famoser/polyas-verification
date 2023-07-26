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

class DeviceParameters
{
    public static function createJsonFingerprint(string $deviceParametersJson): string
    {
        return hash('sha512', $deviceParametersJson);
    }

    public static function createFingerprint(array $deviceParameters): string
    {
        $json = json_encode($deviceParameters);

        return hash('sha512', $json);
    }
}
