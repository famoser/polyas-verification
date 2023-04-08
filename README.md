# POLYAS verification

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE) 
[![PHP Composer](https://github.com/famoser/polyas-verification/actions/workflows/php.yml/badge.svg)](https://github.com/famoser/polyas-verification/actions/workflows/php.yml)
[![Node.js Encore](https://github.com/famoser/polyas-verification/actions/workflows/node.js.yml/badge.svg)](https://github.com/famoser/polyas-verification/actions/workflows/node.js.yml)

Allows to verify POLYAS 3.0 (Version 1.3, February 20, 2023). This is a work in progress. 

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

## Specification

1. (B.1, page 50) What is the hexadecimal representation of the `RECEIPT-VERIFICATION-KEY` RSA public key? I would propose to use PEM PUBLIC KEY representation, which is also what OpenSSL takes as an argument (see https://www.rfc-editor.org/rfc/rfc7468#section-13, which in turn requires an ASN.1 SubjectPublicKeyInfo encoding https://www.rfc-editor.org/rfc/rfc5280#section-4.1.2.7).
2. (B.2, page 52) The example represents `proofOfKnowledgeOfEncryptionCoins.[].c`, `proofOfKnowledgeOfEncryptionCoins.[].f` and , `proofOfKnowledgeOfPrivateCredential.c` within 21 bytes (with the first byte being 0); but `proofOfKnowledgeOfPrivateCredential.f` within 20 bytes (with no first zero-byte). Could you please clarify what the logic here is?
3. (B.2, page 52) The conventions in Section A.1.2 refer to SHA512, while SHA256 is specified to be used.

Suggestions:
1. As it is designed right now, each ballot has to be hashed to find the one ballot with the correct fingerprint. How about including the fingerprint in the ballot entry, too? The second device can still verify that the fingerprint actually corresponds to the ballot, while it is much faster to discover the correct ballot. 
2. The different formats needed for the ballot entry digest (label = UTF-8, ciphertext = byte array, proof of knowledge = long number) increase burden of implementation, and possibly lead to hard-to-debug mistakes. Would it be instead possible to simply everywhere provide hex?

