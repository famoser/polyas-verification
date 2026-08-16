<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Api;

use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use Famoser\PolyasVerification\Crypto\POLYAS\DeviceParameters;
use Famoser\PolyasVerification\PathHelper;
use Famoser\PolyasVerification\Storage;
use Famoser\PolyasVerification\Workflow\ApiClient;
use Famoser\PolyasVerification\Workflow\DownloadReceipt;
use Famoser\PolyasVerification\Workflow\ElectionDetails;
use Famoser\PolyasVerification\Workflow\ExportReceipts;
use Famoser\PolyasVerification\Workflow\Mock\DownloadReceiptMock;
use Famoser\PolyasVerification\Workflow\Mock\StoreReceiptMock;
use Famoser\PolyasVerification\Workflow\Mock\VerificationMock;
use Famoser\PolyasVerification\Workflow\StoreReceipt;
use Famoser\PolyasVerification\Workflow\Verification;
use Famoser\PolyasVerification\Workflow\VerifyReceipt;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\UploadedFile;
use Slim\Routing\RouteCollectorProxy;
use ZipArchive;

class RouteFactory
{
    /**
     * @param RouteCollectorProxy<ContainerInterface> $route
     */
    public static function addRoutes(RouteCollectorProxy $route): void
    {
        $route->get('/election', function (Request $request, Response $response) {
            $election = self::getElection();

            $deviceParameters = self::getDeviceParameters();
            $election['deviceParametersFingerprint'] = $deviceParameters->createFingerprint();

            return SlimExtensions::createJsonResponse($request, $response, $election);
        });

        $route->get('/electionDetails', function (Request $request, Response $response) {
            $apiClient = self::createPOLYASApiClient();
            $electionDetails = new ElectionDetails($apiClient);
            $election = $electionDetails->get();

            return SlimExtensions::createJsonResponse($request, $response, $election);
        });

        $route->get('/ballots', function (Request $request, Response $response) {
            $deviceParameters = self::getDeviceParameters();

            return SlimExtensions::createJsonResponse($request, $response, $deviceParameters->getBallots());
        });

        $route->get('/export/receipts.zip', function (Request $request, Response $response) {
            RequestValidatorExtensions::checkApiKey($request);

            $election = RouteFactory::getElection();
            $exportReceipts = new ExportReceipts($election['polyasElection']);
            if (!$exportReceipts->exportAll($pdfs, $error)) {
                return SlimExtensions::createStatusJsonResponse($request, $response, false);
            }

            $pdfs["README.txt"] = "Generiert " . date('c') . " für " . $election['polyasElection'];
            $filename = "export_receipts_" . date('Y-m-d_H-i-s') . '.zip';
            return SlimExtensions::createZipFileResponse($request, $response, $pdfs, $filename);
        });

        $route->post('/receipt/file/verify', function (Request $request, Response $response) {
            /** @var UploadedFile|false $file */
            $file = current($request->getUploadedFiles());
            if (!$file) {
                throw new HttpBadRequestException($request, 'No file uploaded');
            }
            RequestValidatorExtensions::checkPdfFileUploadSuccessful($request, $file);
            $path = Storage::writeUploadedFile(PathHelper::VAR_TRANSIENT_DIR, $file);

            $deviceParameters = self::getDeviceParameters();

            $receipt = new VerifyReceipt($deviceParameters->getVerificationKey());
            $result = $receipt->verify($path, $validReceipt, $failedCheck);
            Storage::removeFile($path);

            return SlimExtensions::createStatusJsonResponse($request, $response, $result, $failedCheck, null, $validReceipt);
        });

        $route->post('/receipt/download', function (Request $request, Response $response) {
            $payload = SlimExtensions::parseJsonRequestBody($request);
            RequestValidatorExtensions::checkReceipt($request, $payload);
            /** @var array{
             *     'fingerprint': string,
             *     'signature': string,
             *     'ballotVoterId': string,
             * } $payload
             */
            if (DownloadReceiptMock::isMockPayload($payload)) {
                $result = DownloadReceiptMock::performMockDownloadReceipt($payload, $pdf);
            } else {
                $deviceParameters = self::getDeviceParameters();
                $election = self::getElection();

                $storeReceipt = new DownloadReceipt($deviceParameters->getVerificationKey(), $election['polyasElection']);
                $result = $storeReceipt->store($payload, $pdf);
            }

            return SlimExtensions::createPdfFileResponse($response, $result, 'receipt.pdf', $pdf);
        });

        $route->post('/receipt', function (Request $request, Response $response) {
            $payload = SlimExtensions::parseJsonRequestBody($request);
            RequestValidatorExtensions::checkReceipt($request, $payload);
            /** @var array{
             *     'fingerprint': string,
             *     'signature': string,
             *     'ballotVoterId': string,
             * } $payload
             */
            if (StoreReceiptMock::isMockPayload($payload)) {
                $result = StoreReceiptMock::performMockStoreReceipt($payload, $failedCheck);
            } else {
                $deviceParameters = self::getDeviceParameters();
                $election = self::getElection();

                $storeReceipt = new StoreReceipt($deviceParameters->getVerificationKey(), $election['polyasElection']);
                $result = $storeReceipt->store($payload, $failedCheck);
            }

            return SlimExtensions::createStatusJsonResponse($request, $response, $result, $failedCheck);
        });

        $route->post('/verification', function (Request $request, Response $response) {
            $payload = SlimExtensions::parseJsonRequestBody($request);
            RequestValidatorExtensions::checkVerification($request, $payload);
            /** @var array{
             * 'c': string,
             * 'd': string,
             * 'vid': string,
             * 'nonce': string,
             * 'password': string,
             * } $payload
             */
            if (VerificationMock::isMockPayload($payload)) {
                $status = VerificationMock::performMockVerification($payload, $payload['password'], $failedCheck, $validReceipt, $hexBallot);
            } else {
                $deviceParameters = self::getDeviceParameters();
                $election = self::getElection();

                $apiClient = self::createPOLYASApiClient();
                $verification = new Verification($deviceParameters, $apiClient, $election['polyasElection']);
                $challengeCommit = ChallengeCommit::createWithRandom();
                $status = $verification->verify($payload, $payload['password'], $challengeCommit, $validReceipt, $hexBallot, $failedCheck);
            }

            return SlimExtensions::createStatusJsonResponse($request, $response, $status, $failedCheck, $hexBallot, $validReceipt);
        });
    }

    private static function getDeviceParameters(): DeviceParameters
    {
        $deviceParametersPath = PathHelper::PARAMETERS_WITH_FINGERPRINT_JSON_FILE;
        $deviceParametersJson = Storage::readFile($deviceParametersPath);

        return DeviceParameters::createFromFingerprintedJson($deviceParametersJson);
    }

    /**
     * @return array{
     *      'organizer': string,
     *       'election': string,
     *       'period': string,
     *       'link': string,
     *       'polyasElection': string,
     * }
     */
    public static function getElection(): array
    {
        $electionJsonPath = PathHelper::ELECTION_JSON_FILE;

        return Storage::readJsonFile($electionJsonPath); // @phpstan-ignore-line
    }

    private static function createPOLYASApiClient(): ApiClient
    {
        $path = PathHelper::ELECTION_JSON_FILE;
        $content = Storage::readJsonFile($path);

        return new ApiClient($content['polyasElection']);
    }
}
