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

