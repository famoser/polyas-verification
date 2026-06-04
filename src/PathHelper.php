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

class PathHelper
{
    public const string VAR_DIR = __DIR__ . '/../var';
    public const string VAR_TRANSIENT_DIR = self::VAR_DIR . '/transient';
    public const string VAR_PERSISTENT_DIR = self::VAR_DIR . '/persistent';
    public const string ELECTION_JSON_FILE = self::VAR_DIR . '/config/election.json';
    public const string PARAMETERS_WITH_FINGERPRINT_JSON_FILE = self::VAR_DIR . '/config/secondDeviceParametersFingerprint.json';
}
