#!/usr/bin/env php
<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$projectDir = dirname(realpath(__DIR__));
$autoloadRelativePath = 'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$autoloadPath = $projectDir.$autoloadRelativePath;
chdir($autoloadPath);
require $autoloadRelativePath;

function writeErrorAndExit(string $error): void
{
    fwrite(STDERR, $error);
    exit(1);
}

use Famoser\PolyasVerification\Api\RouteFactory;
use Famoser\PolyasVerification\Workflow\ExportReceipts;
use Symfony\Component\Dotenv\Dotenv;

if (file_exists('.env')) {
    $dotenv = new Dotenv();
    $dotenv->loadEnv('.env');
}

$election = RouteFactory::getElection();
$exportReceipts = new ExportReceipts($election['polyasElection']);
if (!$exportReceipts->exportAll($error, $pdfs)) {
    writeErrorAndExit($error);
}

$zip = new ZipArchive();
$filename = 'export_receipts.zip';

if (true !== $zip->open($filename, ZipArchive::CREATE)) {
    writeErrorAndExit('cannot open '.$filename);
}

foreach ($pdfs as $index => $pdf) {
    $zip->addFromString('receipt'.$index.'.pdf', $pdf);
}
$zip->close();
