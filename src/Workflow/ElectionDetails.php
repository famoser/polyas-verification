<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Workflow;

use GuzzleHttp\Exception\GuzzleException;

readonly class ElectionDetails
{
    public function __construct(private ApiClient $apiClient)
    {
    }

    /**
     * @return array{
     *      'title': array{'default': string},
     *  }|null
     */
    public function get(): ?array
    {
        try {
            $electionResponse = $this->apiClient->getElection();
        } catch (GuzzleException) {
            $electionResponse = null;
        }

        return $electionResponse;
    }
}
