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
chdir($projectDir);

$autoloadPath = $projectDir.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require $autoloadPath;

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
if (!$exportReceipts->exportAll($pdfs, $error)) {
    writeErrorAndExit($error);
}

$zip = new ZipArchive();
$targetFilePath = $projectDir.DIRECTORY_SEPARATOR.'export_receipts.zip';
if (true !== $zip->open($targetFilePath, ZipArchive::CREATE)) {
    writeErrorAndExit('cannot open '.$targetFilePath);
}

$zip->addFromString('README.MD', 'Exported receipts at '.time());
foreach ($pdfs as $index => $pdf) {
    $zip->addFromString('receipt'.$index.'.pdf', $pdf);
}
$zip->close();

echo 'File written to '.realpath($targetFilePath)." with ".count($pdfs)." receipts.\n";
