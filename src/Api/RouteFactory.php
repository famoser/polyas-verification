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
use Famoser\PolyasVerification\PathHelper;
use Famoser\PolyasVerification\Storage;
use Famoser\PolyasVerification\Workflow\ApiClient;
use Famoser\PolyasVerification\Workflow\DownloadReceipt;
use Famoser\PolyasVerification\Workflow\Mock\VerificationMock;
use Famoser\PolyasVerification\Workflow\StoreReceipt;
use Famoser\PolyasVerification\Workflow\Verification;
use Famoser\PolyasVerification\Workflow\VerifyReceipt;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\UploadedFile;
use Slim\Routing\RouteCollectorProxy;

class RouteFactory
{
    public static function addRoutes(RouteCollectorProxy $route): void
    {
        $route->get('/election', function (Request $request, Response $response, array $args) {
            $electionJsonFile = PathHelper::ELECTION_JSON_FILE;
            $election = Storage::readJsonFile($electionJsonFile);

            return SlimExtensions::createJsonResponse($request, $response, $election);
        });

        $route->get('/electionDetails', function (Request $request, Response $response, array $args) {
            $apiClient = self::createPOLYASApiClient();
            $election = $apiClient->getElection();

            return SlimExtensions::createJsonResponse($request, $response, $election);
        });

        $route->get('/ballots', function (Request $request, Response $response, array $args) {
            $deviceParametersJsonFile = PathHelper::DEVICE_PARAMETERS_JSON_FILE;
            $deviceParameters = Storage::readJsonFile($deviceParametersJsonFile);

            return SlimExtensions::createJsonResponse($request, $response, $deviceParameters['ballots']);
        });

        $route->post('/receipt', function (Request $request, Response $response, array $args) {
            /** @var UploadedFile|false $file */
            $file = current($request->getUploadedFiles());
            if (!$file) {
                throw new HttpBadRequestException($request, 'No file uploaded');
            }
            RequestValidatorExtensions::checkPdfFileUploadSuccessful($request, $file);
            $path = Storage::writeUploadedFile(PathHelper::VAR_TRANSIENT_DIR, $file);

            $deviceParametersPath = PathHelper::DEVICE_PARAMETERS_JSON_FILE;
            $deviceParameters = Storage::readJsonFile($deviceParametersPath);

            $receipt = new VerifyReceipt($deviceParameters['verificationKey']);
            $result = $receipt->verify($path, $failedCheck, $validReceipt);
            Storage::removeFile($path);

            return SlimExtensions::createStatusJsonResponse($request, $response, $result, $failedCheck, null, $validReceipt);
        });

        $route->post('/receipt/store', function (Request $request, Response $response, array $args) {
            $payload = SlimExtensions::parseJsonRequestBody($request);
            RequestValidatorExtensions::checkReceipt($request, $payload);
            /** @var array{
             *     'fingerprint': string,
             *     'signature': string,
             * } $payload
             */
            $deviceParametersPath = PathHelper::DEVICE_PARAMETERS_JSON_FILE;
            $deviceParameters = Storage::readJsonFile($deviceParametersPath);

            $storeReceipt = new StoreReceipt($deviceParameters['verificationKey']);
            $result = $storeReceipt->store($payload, $failedCheck);

            return SlimExtensions::createStatusJsonResponse($request, $response, $result, $failedCheck);
        });

        $route->post('/receipt/download', function (Request $request, Response $response, array $args) {
            $payload = SlimExtensions::parseJsonRequestBody($request);
            RequestValidatorExtensions::checkReceipt($request, $payload);
            /** @var array{
             *     'fingerprint': string,
             *     'signature': string,
             * } $payload
             */
            $deviceParametersPath = PathHelper::DEVICE_PARAMETERS_JSON_FILE;
            $deviceParameters = Storage::readJsonFile($deviceParametersPath);

            $storeReceipt = new DownloadReceipt($deviceParameters['verificationKey']);
            $result = $storeReceipt->store($payload, $pdf);

            return SlimExtensions::createPdfFileResponse($response, $result, 'receipt.pdf', $pdf);
        });

        $route->post('/verification', function (Request $request, Response $response, array $args) {
            $payload = SlimExtensions::parseJsonRequestBody($request);
            RequestValidatorExtensions::checkVerification($request, $payload);
            /** @var array{
             *     'payload': string,
             *     'voterId': string,
             *     'nonce': string,
             *     'password': string,
             * } $payload
             */
            if (VerificationMock::isMockPayload($payload)) {
                $result = VerificationMock::performMockVerification($failedCheck, $validReceipt);
            } else {
                $deviceParametersPath = PathHelper::DEVICE_PARAMETERS_JSON_FILE;
                $deviceParametersJson = Storage::readFile($deviceParametersPath);

                $apiClient = self::createPOLYASApiClient();
                $verification = new Verification($deviceParametersJson, $apiClient);
                $challengeCommit = ChallengeCommit::createWithRandom();
                $result = $verification->verify($payload, $challengeCommit, $failedCheck, $validReceipt);
            }

            return SlimExtensions::createStatusJsonResponse($request, $response, null !== $result, $failedCheck, $result, $validReceipt);
        });
    }

    public static function createPOLYASApiClient(): ApiClient
    {
        $path = PathHelper::ELECTION_JSON_FILE;
        $content = Storage::readJsonFile($path);

        return new ApiClient($content['polyasElection']);
    }
}
