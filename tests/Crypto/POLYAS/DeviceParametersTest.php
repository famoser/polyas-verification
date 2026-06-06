<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Crypto\POLYAS;

use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use PHPUnit\Framework\TestCase;

class DeviceParametersTest extends TestCase
{
    public function testCompareDeviceParameters(): void
    {
        $deviceParameters = Ballot0::getDeviceParameters();
        $message = Ballot0::getLoginResponseInitialMessage();

        $this->assertTrue($deviceParameters->compareDeviceParameters($message['secondDeviceParametersJson']));
    }
}
