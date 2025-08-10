# POLYAS verification

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE) 
[![PHP Composer](https://github.com/famoser/polyas-verification/actions/workflows/php.yml/badge.svg)](https://github.com/famoser/polyas-verification/actions/workflows/php.yml)
[![Node.js Encore](https://github.com/famoser/polyas-verification/actions/workflows/node.js.yml/badge.svg)](https://github.com/famoser/polyas-verification/actions/workflows/node.js.yml)

When voting over the internet, the voter needs to be able to verify that their vote was cast correctly. This avoids trusting their voting device fully. This project implements voter-side verification (also called individual verification) for POLYAS 3.0 (Version 1.3.2, 31 July 2023), following the second device spec ([Version 1.2-SNAPSHOT](https://github.com/polyas-voting/core3-verifiable-doc/blob/a43a805ab89d95acb5acdecb87415dd7473168e2/cai/second-device-spec.md), 22. September 2024). You can find the specification in the folder [spec](./spec); this folder is excluded from the MIT license (the copyright remains at POLYAS). Note that the POLYAS system does only publish part of their specification (and notably no code and no formal proofs of the full system).

This project has been developed for the [Gesellschaft für Informatik](https://gi.de/) (German) and has been supported by the Université de Lorraine, CNRS, Inria, and LORIA (Nancy, France). In collaboration with other researchers, a scientific publication documents the experience of implementing this verifier (and others), and formulates recommendation for similar projects ([paper](https://inria.hal.science/hal-04663997)). 

<img src="assets/0_index.png?raw=true" alt="Screenshot start" width="30%">


## Verification UI

After voting in the main voting system, the voter is presented with a QR code. Using this QR code, the voter is able to initialize a second device application (such as this project). The second device application then downloads the cast vote from the server, and shows it to the voter again. The voter can then verify their vote has been cast correctly.

<table>
    <tbody>
        <tr>
            <td>Start</td>
            <td>Enter password</td>
        </tr>
        <tr>
            <td><img src="assets/1_verify_0_index.png?raw=true" alt="Screenshot verification start"></td>
            <td><img src="assets/1_verify_1_password.png?raw=true" alt="Screenshot password enter"></td>
        </tr>
    </tbody>
</table>

<table>
    <tbody>
        <tr>
            <td>Verify ballot voter id (to check indeed verifying own vote)</td>
            <td>Verify ballot content (to check cast correctly)</td>
        </tr>
        <tr>
            <td><img src="assets/1_verify_2_ballot_voter_id.png?raw=true" alt="Screenshot ballot voter id verification"></td>
            <td><img src="assets/1_verify_3_ballot_content.png?raw=true" alt="Screenshot ballot content verification"></td>
        </tr>
    </tbody>
</table>

After successful vote validation, there is an option to download the receipt or skip the step. An example of such a receipt is in `assets/generated_receipt.pdf`.

<img src="assets/1_verify_4_receipt_store.png?raw=true" alt="Screenshot store receipt" width="50%">

You can test this UI with the link as follows:
- Enter the following link: http://localhost:5173/verify?c=7bgIHYQotKLc8tgCbWp5yuc83xSbN-JV4Vwpnb50qyIzNUj2tYDYzPInG80WJ1mf2tB8BstZXWH_b0y4&vid=voter3&nonce=f299af96450db626754147aa132237bbf5603df2eea8215a0859288df8015c85
- Enter the password 123456

Then mock data client will kick in, which validates the vote.


## Receipt UI

The second device application outputs a receipt, which contains the fingerprint of the ballot, and a signature over said fingerprint. The receipt is typically sent to the auditors, which ensure then a ballot with this fingerprint is considered in the final voting result.

This project also allows to verify that signature; thereby proving that the voting server must know the referenced ballot.

<table>
    <tbody>
        <tr>
            <td>Start</td>
            <td>Valid receipt</td>
        </tr>
        <tr>
            <td><img src="assets/2_receipt_0_index.png?raw=true" alt="Screenshot receipt start"></td>
            <td><img src="assets/2_receipt_1_success.png?raw=true" alt="Screenshot receipt valid"></td>
        </tr>
    </tbody>
</table>

<table>
    <tbody>
        <tr>
            <td>Invalid receipt</td>
            <td>Receipt help</td>
        </tr>
        <tr>
            <td><img src="assets/2_receipt_2_error.png?raw=true" alt="Screenshot receipt error"></td>
            <td><img src="assets/2_receipt_3_help.png?raw=true" alt="Screenshot receipt help"></td>
        </tr>
    </tbody>
</table>

You can test this UI with the receipt in `assets/test_vote_receipt.pdf`.

## Develop

The backend is built using `php` (using the `slim` framework) and manages dependencies using `composer`. Quick start:
- `composer install`
- `symfony server:start` (see [symfony CLI](https://symfony.com/download))

The frontend is built using `JavaScript` (using `vue` with `TypeScript`), manages dependencies using `npm`, and builds using `vite`. Quick start:
- `npm install`
- `npm run dev`

Then open `localhost:5173` (pointing to the `vite` server started by `npm run dev`) to see the webpage. Note that only in this local environment, the `vite` server is running, in production `php` serves all files.

## Release & Deploy

`famoser/agnes` is recommended to release and deploy.

You need access to the config repository specified in `agnes.yml`. Then:
- create a new release (here `v1.0`) of `main` branch with `./vendor/bin/agnes release v1.0 main`
- deploy release to `prod` environment with `./vendor/bin/agnes deploy *:*:prod v1.0`

The server needs to fulfil requirements specified in `composer.json`.

## Future functionality

The following changes should be tackled:
- Describe clearly what IV is for, as users are likely not aware (see https://publikationen.bibliothek.kit.edu/1000175514/155244080)
- Use https://github.com/famoser/elliptic/ as a cryptography backend
- Ask POLYAS how to check that uploaded receipts are contained on the bulletin board of the POLYAS server
