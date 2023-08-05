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

class ApiClient
{
    /**
     * @return array{
     *     'title': array{'default': string},
     * }
     */
    public function getElection(): array
    {
        return [
            'title' => ['default' => 'Dummy'],
        ];
    }

    /**
     * @param array{
     *     'voterId': string,
     *     'nonce': string,
     *     'password': string,
     *     'challengeCommitment': string,
     * } $payload
     *
     * @return null|array{
     *      'token': string,
     *      'ballotVoterId': string,
     *      'publicLabel': string,
     *      'initialMessage': string
     * }
     */
    public function postLogin(array $payload): ?array
    {
        return [
            'token' => 'MDIwNWJmMmUxNDQ5NmY2OGMwZjg2ZjZiMzEzZjIxMGE5MzkzZWRiMDgzODIxZGNjNGY5OTE0Y2FiOWM1MWM5ZjJl.UjVXYXRxTlRzdk12QWRwOA==',
            'ballotVoterId' => '0205bf2e14496f68c0f86f6b313f210a9393edb083821dcc4f9914cab9c51c9f2e',
            'publicLabel' => 'A',
            'initialMessage' => '',
        ];
    }

    /**
     * @param array{
     *     'challenge': string,
     *     'challengeRandomCoin': string,
     * } $payload
     *
     * @return null|array{
     *      'z': string[]
     * }
     */
    public function postChallenge(array $payload, string $authenticationToken): ?array
    {
        return [
            'z' => [
                '3633826251616834446657553661530373736489206587264246793596555854504147120873052400272122845815239659486740186516083053240689380948861192914781931033170662',
            ],
        ];
    }
}
