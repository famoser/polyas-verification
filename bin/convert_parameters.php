<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$dir = __DIR__.'/../.agnes/hostpoint/polyas-verification.famoser.ch/prod/var/config';
$secondDeviceParametersFingerprintJson = file_get_contents($dir.'/secondDeviceParametersFingerprint.json');
$secondDeviceParametersFingerprint = json_decode($secondDeviceParametersFingerprintJson, true);
$publicParametersJson = $secondDeviceParametersFingerprint['publicParametersJson'];
file_put_contents($dir.'/deviceParameters.json', $publicParametersJson);
