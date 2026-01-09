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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    private Client $client;

    public function __construct(string $baseUrl)
    {
        $this->client = new Client([
            'base_uri' => $baseUrl.'/ssd/rest/',
            'headers' => ['User-Agent' => 'famoser-polyas-verification/1.0'],
        ]);
    }

    /**
     * @return array{
     *     'title': array{'default': string},
     * }|null
     *
     * @throws GuzzleException
     */
    public function getElection(): ?array
    {
        $response = $this->client->get('electionData');

        return $this->returnBody($response);
    }

    /**
     * @param array{
     *     'voterId': string,
     *     'nonce': string,
     *     'password': string,
     *     'challengeCommitment': string,
     * } $payload
     *
     * @return array{
     *      'token': string,
     *      'ballotVoterId': string,
     *      'publicLabel': string,
     *      'initialMessage': string
     * }|null
     *
     * @throws GuzzleException
     */
    public function postLogin(array $payload): ?array
    {
        $response = $this->client->post('login', ['json' => $payload]);

        return $this->returnBodyValue($response);
    }

    /**
     * @param array{
     *     'challenge': string,
     *     'challengeRandomCoin': string,
     * } $payload
     *
     * @return array{
     *      'z': string[]
     * }|null
     *
     * @throws GuzzleException
     */
    public function postChallenge(array $payload, string $authenticationToken): ?array
    {
        $response = $this->client->post('challenge', [
            'json' => $payload,
            'headers' => ['AuthToken' => $authenticationToken],
        ]);

        return json_decode($this->returnBodyValue($response), true);
    }

    private function returnBody(ResponseInterface $response): mixed
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    private function returnBodyValue(ResponseInterface $response): mixed
    {
        /** @var array{status: string, value: mixed} $body */
        $body = $this->returnBody($response);
        if ('OK' !== $body['status']) {
            return null;
        }

        return $body['value'];
    }
}
