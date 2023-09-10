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
    public const VAR_DIR = __DIR__.'/../var';
    public const VAR_TRANSIENT_DIR = self::VAR_DIR.'/transient';
    public const VAR_PERSISTENT_DIR = self::VAR_DIR.'/persistent';
    public const ELECTION_JSON_FILE = self::VAR_DIR.'/config/election.json';
    public const DEVICE_PARAMETERS_JSON_FILE = self::VAR_DIR.'/config/deviceParameters.json';
}
