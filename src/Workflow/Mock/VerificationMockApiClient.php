<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Workflow\Mock;

use Famoser\PolyasVerification\Workflow\ApiClient;

/**
 * Used to test the verification procedure without needing an active election.
 */
class VerificationMockApiClient extends ApiClient
{
    public function __construct()
    {
        parent::__construct('mock');
    }

    public function postLogin(array $payload): ?array
    {
        $json = file_get_contents(__DIR__.'/loginResponse.json');

        return json_decode($json, true);
    }

    public function postChallenge(array $payload, string $authenticationToken): ?array
    {
        $json = file_get_contents(__DIR__.'/challengeResponse.json');

        return json_decode($json, true);
    }
}
