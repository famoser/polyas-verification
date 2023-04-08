<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\DER;

use Sop\ASN1\Type\UnspecifiedType;

class Decoder
{
    public static function asRSAPublicKey(string $der): RSAPublicKey
    {
        // of the form SubjectPublicKeyInfo (see https://www.rfc-editor.org/rfc/rfc7468#section-13)
        $derDecoded = UnspecifiedType::fromDER($der);

        // SubjectPublicKeyInfo ::= SEQUENCE { algorithm AlgorithmIdentifier, subjectPublicKey BIT STRING } (see https://www.rfc-editor.org/rfc/rfc5280#section-4.1)
        $subjectPublicKeyInfo = $derDecoded->asSequence();

        // AlgorithmIdentifier ::= SEQUENCE { algorithm OBJECT IDENTIFIER, parameters ANY DEFINED BY algorithm OPTIONAL } (see https://www.rfc-editor.org/rfc/rfc5280#section-4.1.1.2)
        $algorithmIdentifier = $subjectPublicKeyInfo->at(0)->asSequence();

        // expect object identifier to be 1.2.840.113549.1.1.1 (see https://www.rfc-editor.org/rfc/rfc3279#section-2.3.1)
        $objectIdentifier = $algorithmIdentifier->at(0)->asObjectIdentifier();
        if ('1.2.840.113549.1.1.1' !== $objectIdentifier->oid()) {
            throw new DecodingException('Object identifier does not match RSA public key: '.$objectIdentifier->oid());
        }

        // expect to be of null type (see https://www.rfc-editor.org/rfc/rfc3279#section-2.3.1)
        $algorithmIdentifier->at(1)->asNull();

        $subjectPublicKey = $subjectPublicKeyInfo->at(1)->asBitString();

        // The RSA public key MUST be encoded using the ASN.1 type RSAPublicKey (see https://www.rfc-editor.org/rfc/rfc3279#section-2.3.1)
        // RSAPublicKey ::= SEQUENCE { modulus INTEGER, publicExponent INTEGER }
        $subjectPublicKeyDecoded = UnspecifiedType::fromDER($subjectPublicKey);
        $rsaPublicKey = $subjectPublicKeyDecoded->asSequence();
        $n = $rsaPublicKey->at(0)->asInteger();
        $e = $rsaPublicKey->at(1)->asInteger();

        return new RSAPublicKey($n->number(), $e->number());
    }
}
