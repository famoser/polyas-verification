<?php

use Famoser\PolyasVerification\Crypto\RSA;
use Famoser\PolyasVerification\Test\Utils\RSATest;

require dirname(__DIR__).'/vendor/autoload.php';

try {
    RSA\Key::generateRSAKey(RSATest::TEST_RSA_KEY_BITS);
} catch (Exception $exception) {
    var_dump("Warning: RSA Key generation failed");
}
