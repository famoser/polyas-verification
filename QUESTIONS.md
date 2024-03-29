# Questions

This document tracks open and answered questions. The answers are from POLYAS directly, and reflect their opinions; while they are reformulated for conciseness.

The questions refer either to the POLYAS 3.0 specification (shortened as `spec`) or to the second device spec (shortened as `2ndDevice`).

## Specification

The questions in this chapter block the implementation of the verifier.


1. ✅ (`spec`, B.1, page 50) What is the hexadecimal representation of the `RECEIPT-VERIFICATION-KEY` RSA public key? I would propose to use PEM PUBLIC KEY representation, which is also what OpenSSL takes as an argument (see https://www.rfc-editor.org/rfc/rfc7468#section-13, which in turn requires an ASN.1 SubjectPublicKeyInfo encoding https://www.rfc-editor.org/rfc/rfc5280#section-4.1.2.7).

> The format is X.509, represented as a hex string.
> PEM was designed for stand-alone files, but here the key is transmitted within a JSON. Note how PEM encapsulates X.509.

2. ⏳ (`spec`, B.2, page 52) The example represents `proofOfKnowledgeOfEncryptionCoins.[].c`, `proofOfKnowledgeOfEncryptionCoins.[].f` and , `proofOfKnowledgeOfPrivateCredential.c` within 21 bytes (with the first byte being 0); but `proofOfKnowledgeOfPrivateCredential.f` within 20 bytes (with no first zero-byte). Could you please clarify what the logic here is?

> The .f and .c fields of the zero-knowledge proofs are of type BigInteger and are represented as decimal numbers (without leading zeros).

3. ✅ (`2ndDevice`, Integrity of the second device public parameters) How to serialize & hash device parameter json, considering that json serialization is not unique over platforms?

> Directly use the property `secondDeviceParametersJson` of the `loginResponse["initialMessage"]`; without deserializing it first. 

4. ⏳ (`2ndDevice`, Checking the acknowledgement) The provided testvector's signature does not verify in the current implementation. Can you please clarify the wording over what data is the signature (the whole `BTBS`, or only over `ballotAsNormalizedBytestring`)? Can you provide a complete testvector (including private keys) to align implementations?

5. ⏳ (`2ndDevice`, Decrypting the QR-code) The provided testvector's `comKey` is different than to what the current implementation outputs. Can you please provide the `key_derivation_key`? Further, the API call in `2ndDevice` requests `256` bits, but `spec` specifies algorithm 1 to have `length` specified in bytes. Can you please align the API call / definition?

5. ⏳ (`2ndDevice`, Decrypting the QR-code) The provided QR code `c` value presumably uses https://base64.guru/standards/base64url, not original base64. Can you please clarify this?

## Organisational questions

1. I would like to include the specification document next to my source code, i.e. publish it to GitHub. Is this possible?
2. There are a couple of typos in the specification document. I'd prefer to fix them directly and create a PR against the documentation; saves time on both ends. Is this possible?

## Security questions

Questions to understand the design principles behind the protocol.

1. Is there a computational and/or symbolic proof over the presented protocol?
> No. A preprint of the paper describing the essential idea is available here: https://arxiv.org/pdf/2304.09456.pdf.

2. Any particular reason why `secp256k1` is chosen, and not `ed25519`?
> `secp256k1` is of prime order, as required by the zero-knowledge proofs. `ed25519` is not of a prime order.

3. Is the receipt generated on the client, or on the server?
> The receipt is generated by the second device application; hence client-side.

4. To reach cryptographic receipt-freeness, the second device has to implement ZKP and other complex constructs. Is this really worth it, considering that the user can simply take a screenshot of the decrypted vote (which is not a formal receipt, but possibly sufficient for the adversary).
> Reaching formal guarantees here is worth the effort. Further, the user can easily change the displayed vote on the second device, hence faking the screenshot for the adversary is easy. 

## Improvement suggestions

### Specification

1. (`spec`, B.2, page 52) The conventions in Section A.1.2 refer to SHA512, while SHA256 is specified to be used.
2. (`spec`, B.2, page 52) Please specify the unicode of the receipt markers.
3. Complete test data for every algorithm, and for a full run of a protocol would help to diagnose errors when implementing the verifier. In particular, testdata *before* inserting it into a pseudo-random function such as `HMAC` is useful. 
4. A reference implementation of specified algorithms would help to resolve ambiguities.
5. (`2ndDevice`) Specify the encoding of the curve points. It seems to be ANSI X9.62 4.3.6. which is behind a paywall, but explaining it is easy (prefix 02 if even; else prefix 03; followed by x coordinate). See implementation [here](https://github.com/famoser/polyas-verification/blob/ab0698a26e9063f49e0324b54a4cd8ec20bec52e/src/Crypto/SECP256K1/Encoder.php).
6. (`2ndDevice`, Checking the acknowledgement) specifies to use `voterId` when calculating the ballot digest; but should instead refer to `ballotVoterId`.
7. (`2ndDevice`, `spec`) are inconsistently refering to either `voterId` or `voterID`.
8. (`2ndDevice`, Decrypting the QR-code) clarify that the IV and the tag are prepended to the ciphertext.
9. (`2ndDevice`, Decrypting the QR-code) incude the information that the QR code `c` is in https://base64.guru/standards/base64url, not original base64.

### Organization

Publishing the following information public would make implementation easier / more transparent:
- Specification (referring to both `spec` and `2ndDevice`)
- Proofs (both computational as well as symbolic)
- OpenAPI spec of the API
- Code of the Verifier and/or whole of POLYAS

### Protocol

1. To check whether a receipt of a ballot fingerprint corresponds to a ballot registered at the voting server, every registered ballot has to be hashed to find the one ballot with the correct fingerprint. How about including the fingerprint in the ballot entry, too? The second device can still verify that the fingerprint actually corresponds to the ballot, while it is much faster to discover the correct ballot.
2. The different formats needed for the ballot entry digest (label = UTF-8, ciphertext = byte array, proof of knowledge = long number) increase burden of implementation, and possibly lead to hard-to-debug mistakes. How about everywhere using HEX?
3. The receipt in the PDF file uses two minus `--` as an encapsulation boundary marker. Why not use the PEM encapsulation marker (five `-----`) so the receipt is a valid PEM file? Then a PEM parser can be reused; and I think it changes nothing about PDF validity.
4. After opening the QR code on the second device, the voter additionally has to enter a short nonce (changes every 30 seconds). This nonce could instead directly be embedded into the QR code, simplifying both UI as well as UX. 
