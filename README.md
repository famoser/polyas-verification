# POLYAS verification

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE) 
[![PHP Composer](https://github.com/famoser/polyas-verification/actions/workflows/php.yml/badge.svg)](https://github.com/famoser/polyas-verification/actions/workflows/php.yml)
[![Node.js Encore](https://github.com/famoser/polyas-verification/actions/workflows/node.js.yml/badge.svg)](https://github.com/famoser/polyas-verification/actions/workflows/node.js.yml)

**Maintenance is paused as election using this verifier is over. Maintenance will resume when the verifier is used again.**

Allows to verify POLYAS 3.0 (Version 1.3.2, 31 July 2023), following the second device spec (Version 1.1, 09 Februar 2024). You can find the specification in the folder [spec](./spec); this folder is excluded from the MIT license (the copyright remains at POLYAS).

<img src="assets/0_index.png?raw=true" alt="Screenshot start" width="30%">

## Verification UI

After voting, the voter is presented with a QR code, to be able to enter a second device application.
The second device application allows the user to verify their plain vote again, to check that the voting procedure finished with the expected vote.

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
            <td>Verification valid</td>
            <td>Verification invalid</td>
        </tr>
        <tr>
            <td><img src="assets/1_verify_2_success.png?raw=true" alt="Screenshot verification success"></td>
            <td><img src="assets/1_verify_3_fail.png?raw=true" alt="Screenshot verification failed"></td>
        </tr>
    </tbody>
</table>

You can test this UI with the link as follows:
- Enter the following link: http://localhost:4300/?c=7bgIHYQotKLc8tgCbWp5yuc83xSbN-JV4Vwpnb50qyIzNUj2tYDYzPInG80WJ1mf2tB8BstZXWH_b0y4&vid=voter3&nonce=f299af96450db626754147aa132237bbf5603df2eea8215a0859288df8015c85
- Enter the password 123456

Then mock data client will kick in, which validates the vote.

### Receipt

After successful vote validation, there is an option to either store or download the receipt.

<img src="assets/1_verify_4_receipt_store.png?raw=true" alt="Screenshot store receipt" width="50%">

If the user chooses to store the receipt, the UI implies the server operator will verify the referenced vote is indeed part of the final voting result. If the user chooses to download the receipt, the server will generate them a .pdf file with the fingerprint & signature of the vote (example in `assets/generated_receipt.pdf`).

## Receipt UI

The second device application outputs a receipt, which contains the fingerprint of the ballot, and a signature over said fingerprint. 
This UI allows to verify that signature; thereby proving that the voting server must know the referenced ballot.

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

If the receipt validates successfully, the user may choose to store the receipt on the server.

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

## Future functionality

The following features would be nice-to-haves:
- Check whether uploaded receipts are contained on the bulletin board of the POLYAS server
