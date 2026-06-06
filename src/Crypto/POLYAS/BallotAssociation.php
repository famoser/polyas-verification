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

use Famoser\PolyasVerification\Crypto\PEM;
use Famoser\PolyasVerification\Crypto\POLYAS\Utils\PedersenFactory;
use Famoser\PolyasVerification\Crypto\POLYAS\Utils\Serialization;
use Famoser\PolyasVerification\Crypto\RSA;
use Famoser\PolyasVerification\Crypto\RSA\OpenSSLException;
use Famoser\PolyasVerification\Crypto\SECP256K1\Encoder;
use Mdanter\Ecc\EccFactory;

readonly class BallotAssociation
{
    public function __construct(private string $reference, private string $referenceCoin, private string $vid)
    {
    }

    public function verify(): bool
    {
        $l = gmp_init($this->referenceCoin);

        $q = EccFactory::getSecgCurves()->generator256k1()->getOrder();
        $uniformHash = new UniformHash($q, $this->vid);
        $v = $uniformHash->hash();

        $pedersen = PedersenFactory::createPedersen();
        $commitment =  $pedersen->commit($v, $l);

        $commitmentString = Encoder::compressPoint($commitment);
        /** @var string $commitmentStringBinary */
        $commitmentStringBinary = hex2bin($commitmentString);
        $commitmentStringHash = hash('sha256', $commitmentStringBinary, true);

        $encodedCommitment = base64_encode($commitmentStringHash);
        $formattedReference = substr(chunk_split(substr($encodedCommitment, 0, 22), 6, "-"), 0, -1);
        return $formattedReference === $this->reference;
    }
}
