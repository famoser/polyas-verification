# POLYAS verification

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE) 
[![PHP Composer](https://github.com/famoser/polyas-verification/actions/workflows/php.yml/badge.svg)](https://github.com/famoser/polyas-verification/actions/workflows/php.yml)
[![Node.js Encore](https://github.com/famoser/polyas-verification/actions/workflows/node.js.yml/badge.svg)](https://github.com/famoser/polyas-verification/actions/workflows/node.js.yml)

Allows to verify POLYAS 3.0 (Version 1.3, February 20, 2023). This is a work in progress. 

<table>
    <tbody>
        <tr>
            <td>Start</td>
            <td>Valid receipt</td>
        </tr>
        <tr>
            <td><img src="assets/01_start_view.png?raw=true" alt="Screenshot Start"></td>
            <td><img src="assets/02_receipt_valid.png?raw=true" alt="Screenshot Receipt valid"></td>
        </tr>
    </tbody>
</table>

<table>
    <tbody>
        <tr>
            <td>Start with help</td>
            <td>Invalid receipt</td>
        </tr>
        <tr>
            <td><img src="assets/03_start_view_with_help.png?raw=true" alt="Screenshot Start with expanded help"></td>
            <td><img src="assets/04_receipt_invalid.png?raw=true" alt="Screenshot Receipt invalid"></td>
        </tr>
    </tbody>
</table>

## Develop

The backend is built using `php` (using the `slim` framework) and manages dependencies using `composer`. Quick start:
- `composer install`
- `symfony server:start` (see [symfony CLI](https://symfony.com/download))

The frontend is built using `JavaScript` (using `vue` with `TypeScript`), manages dependencies using `npm`, and builds using `vite`. Quick start:
- `npm install`
- `npm run dev`

Then open `localhost:8000` (pointing to the `php` server started by the `symfony` cli) to see the webpage. Note that in this local environment, the `php` server only responds to `api` calls; otherwise it forwards to the `vite` server. In the production environment, the `vite` server is not running, and `php` serves all files.

## Release & Deploy

`famoser/agnes` is recommended to release and deploy.

You need access to the config repository specified in `agnes.yml`. Then:
- create a new release (here `v1.0`) of `main` branch with `./vendor/bin/agnes release v1.0 main`
- deploy release to `prod` environment with `./vendor/bin/agnes deploy *:*:prod v1.0`

The server needs to fulfil requirements specified in `composer.json`.

## Questions

Specification:
1. (B.1, page 50) What is the hexadecimal representation of the `RECEIPT-VERIFICATION-KEY` RSA public key? I would propose to use PEM PUBLIC KEY representation, which is also what OpenSSL takes as an argument (see https://www.rfc-editor.org/rfc/rfc7468#section-13, which in turn requires an ASN.1 SubjectPublicKeyInfo encoding https://www.rfc-editor.org/rfc/rfc5280#section-4.1.2.7).
2. (B.2, page 52) The example represents `proofOfKnowledgeOfEncryptionCoins.[].c`, `proofOfKnowledgeOfEncryptionCoins.[].f` and , `proofOfKnowledgeOfPrivateCredential.c` within 21 bytes (with the first byte being 0); but `proofOfKnowledgeOfPrivateCredential.f` within 20 bytes (with no first zero-byte). Could you please clarify what the logic here is?
3. (B.2, page 52) The conventions in Section A.1.2 refer to SHA512, while SHA256 is specified to be used.
4. Could you please provide complete test data; e.g. a full run of the protocol including receipts?
5. How will the boards be made accessible, and when/how can this be tested?

Improvement suggestions:
1. As it is designed right now, each ballot has to be hashed to find the one ballot with the correct fingerprint. How about including the fingerprint in the ballot entry, too? The second device can still verify that the fingerprint actually corresponds to the ballot, while it is much faster to discover the correct ballot. 
2. The different formats needed for the ballot entry digest (label = UTF-8, ciphertext = byte array, proof of knowledge = long number) increase burden of implementation, and possibly lead to hard-to-debug mistakes. Would it be instead possible to simply everywhere provide hex?
3. The receipt in the PDF file uses two minus `--` as an encapsulation boundary marker. Why not use the PEM encapsulation marker (five `-----`) so the receipt is a valid PEM file? Then a PEM parser can be reused; and I think it changes nothing about PDF validity. In any case, please specify the unicode of the markers used, as latex will likely not preserve it during render. 

Organisational questions:
1. I would like to include the specification document next to my source code, i.e. publish it to GitHub. Is this possible?
2. There are a couple of typos in the specification document. I'd prefer to fix them directly and create a PR against the documentation; saves time on both ends. Is this possible? 
3. Where is the reference implementation for the universal verifiability procedure?
4. Is there a computational and/or symbolic proof over the presented protocol?
5. Any particular reason why `secp256k1` is chosen, and not `ed25519`?
