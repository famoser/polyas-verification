<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Crypto;

use Famoser\PolyasVerification\Crypto\AES;
use Famoser\PolyasVerification\Test\Utils\IncompleteTestTrait;
use PHPUnit\Framework\TestCase;

class AESTest extends TestCase
{
    use IncompleteTestTrait;

    public function testEncryptionReversable(): void
    {
        $data = 'some data';
        $key = 'some key';

        $ciphertext = AES\Encryption::encryptECB($data, $key);
        $actualData = AES\Encryption::decryptECB($ciphertext, $key);

        $this->assertEquals($data, $actualData);
    }

    public function testDecryption(): void
    {
        $this->markTestIncompleteNS('AES decryption specified only partially.');

        $base64Ciphertext = 'vtWXj-YxxTV2ektefJ5pk7AWc9saoPbu6wJZUZ9R1t8ekU89x7SCYLcg8ODi3fHST4BTmAK97XN3XqWc';
        $keyHex = 'dd96a88777267c645ff14648c9e03f6c9f56652a07fa3bf72e8a5f63f4288307';
        $expectedPlaintextHex = '1e89b5f95deae82f6f823b52709117405f057783eda018d72cbd83141d394fbd';

        $data = base64_decode($base64Ciphertext);
        /** @var string $key */
        $key = hex2bin($keyHex);

        $actualData = AES\Encryption::decryptECB($data, $key);

        $actualDataHex = bin2hex($actualData);
        $this->assertEquals($expectedPlaintextHex, $actualDataHex);
    }

    public function testEncryption(): void
    {
        $this->markTestIncompleteNS('AES encryption specified only partially.');

        $expectedPlaintextHex = '1e89b5f95deae82f6f823b52709117405f057783eda018d72cbd83141d394fbd';
        $keyHex = 'dd96a88777267c645ff14648c9e03f6c9f56652a07fa3bf72e8a5f63f4288307';
        $expectedBase64Ciphertext = 'vtWXj-YxxTV2ektefJ5pk7AWc9saoPbu6wJZUZ9R1t8ekU89x7SCYLcg8ODi3fHST4BTmAK97XN3XqWc';

        /** @var string $data */
        $data = hex2bin($expectedPlaintextHex);
        /** @var string $key */
        $key = hex2bin($keyHex);

        $actualCiphtertext = AES\Encryption::encryptECB($data, $key);

        $actualCiphtertextBase64 = base64_encode($actualCiphtertext);
        $this->assertEquals(bin2hex(base64_decode($expectedBase64Ciphertext)), bin2hex(base64_decode($actualCiphtertextBase64)));
        $this->assertEquals($expectedBase64Ciphertext, $actualCiphtertextBase64);
    }
}
