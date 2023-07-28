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
    /** @var array{
     *     'publicKey': string,
     *     'verificationKey': string,
     *     'ballots': mixed
     * }
     */
    private array $deviceParameters;

    public function __construct(private string $deviceParametersJson)
    {
        $this->deviceParameters = json_decode($this->deviceParametersJson, true);
    }

    public function compareDeviceParameters(string $deviceParametersJson): bool
    {
        // spec specifies to compare the sha512 hash; however not necessary to hash first
        // note how JSON representation is not unique; hence this is why we must directly compare on the json value
        // concretely, the interfacing system is running java, which serialized empty objects as {}, but php uses []
        return $deviceParametersJson === $this->deviceParametersJson;
    }

    public function createFingerprint(): string
    {
        return hash('sha512', $this->deviceParametersJson);
    }

    public function getVerificationKey(): string
    {
        return $this->deviceParameters['verificationKey'];
    }

    public function getPublicKey(): string
    {
        return $this->deviceParameters['publicKey'];
    }
}
