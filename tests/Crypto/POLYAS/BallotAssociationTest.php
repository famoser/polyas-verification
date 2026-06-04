<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\DER;
use Famoser\PolyasVerification\Crypto\PEM;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotAssociation;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigest;
use Famoser\PolyasVerification\Crypto\POLYAS\BallotDigestSignature;
use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use PHPUnit\Framework\TestCase;

class BallotAssociationTest extends TestCase
{
    public function testBallotAssociationVerification(): void
    {
        $reference = Ballot0::getBallotReference();
        $vid = Ballot0::getQRCode()['vid'];
        $referenceCoin = Ballot0::getQRCodeDecrypted()['referenceCoin'];

        $ballotAssociation = new BallotAssociation($reference, $referenceCoin, $vid);
        $this->assertTrue($ballotAssociation->verify());
    }
}
