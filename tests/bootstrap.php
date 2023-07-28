<?php

use Famoser\PolyasVerification\Crypto\RSA;
use Famoser\PolyasVerification\Test\Crypto\RSATest;

require dirname(__DIR__).'/vendor/autoload.php';

// required for GitHub CI on which first key generation fails
try {
    RSA\KeyFactory::generateRSAKey(RSATest::TEST_RSA_KEY_BITS);
} catch (Exception $exception) {
    var_dump("Warning: RSA Key generation failed");
}
