<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Utils;

use Famoser\PolyasVerification\Crypto;
use Famoser\PolyasVerification\Pem;
use PHPUnit\Framework\TestCase;
use Sop\ASN1\Type\UnspecifiedType;

class PemTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testExtractPayloadsPublicKeyPem(): void
    {
        $key = Crypto::generateRSAKey(CryptoTest::TEST_RSA_KEY_BITS);
        $publicKey = Crypto::getPublicKeyPem($key);

        $payloads = [];
        $success = Pem::extractPayloads($publicKey, $payloads, $error);
        $this->assertTrue($success);
        $this->assertNull($error);
        $this->assertCount(1, $payloads);
        $this->assertEquals('PUBLIC KEY', $payloads[0]['label']);

        // of the form SubjectPublicKeyInfo (see https://www.rfc-editor.org/rfc/rfc7468#section-13)
        $derDecoded = UnspecifiedType::fromDER($payloads[0]['payload']);

        // SubjectPublicKeyInfo ::= SEQUENCE { algorithm AlgorithmIdentifier, subjectPublicKey BIT STRING } (see https://www.rfc-editor.org/rfc/rfc5280#section-4.1)
        $subjectPublicKeyInfo = $derDecoded->asSequence();

        // AlgorithmIdentifier ::= SEQUENCE { algorithm OBJECT IDENTIFIER, parameters ANY DEFINED BY algorithm OPTIONAL } (see https://www.rfc-editor.org/rfc/rfc5280#section-4.1.1.2)
        $algorithmIdentifier = $subjectPublicKeyInfo->at(0)->asSequence();

        // expect object identifier to be 1.2.840.113549.1.1.1 (see https://www.rfc-editor.org/rfc/rfc3279#section-2.3.1)
        $objectIdentifier = $algorithmIdentifier->at(0)->asObjectIdentifier();
        $this->assertEquals('1.2.840.113549.1.1.1', $objectIdentifier->oid());

        // expect to be of null type (see https://www.rfc-editor.org/rfc/rfc3279#section-2.3.1)
        $parameters = $algorithmIdentifier->at(1)->asNull();
        $this->assertNotNull($parameters);

        $subjectPublicKey = $subjectPublicKeyInfo->at(1)->asBitString();

        // The RSA public key MUST be encoded using the ASN.1 type RSAPublicKey (see https://www.rfc-editor.org/rfc/rfc3279#section-2.3.1)
        // RSAPublicKey ::= SEQUENCE { modulus INTEGER, publicExponent INTEGER }
        $subjectPublicKeyDecoded = UnspecifiedType::fromDER($subjectPublicKey);
        $rsaPublicKey = $subjectPublicKeyDecoded->asSequence();
        $n = $rsaPublicKey->at(0)->asInteger();
        $e = $rsaPublicKey->at(1)->asInteger();

        $nBitLength = strlen(gmp_strval($n->number(), 2));
        $this->assertEquals(1024, $nBitLength);

        $eBitLength = strlen(gmp_strval($e->number(), 2));
        $this->assertEquals(17, $eBitLength);
    }

    /**
     * @throws \Exception
     */
    public function testExtractPayloadsMultipleSections(): void
    {
        $section1 = 'payload1';
        $section2 = 'payload2';
    }
}
