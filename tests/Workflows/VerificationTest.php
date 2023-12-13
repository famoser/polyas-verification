<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Test\Workflows;

use Famoser\PolyasVerification\Crypto\POLYAS\ChallengeCommit;
use Famoser\PolyasVerification\PDFGenerator;
use Famoser\PolyasVerification\Storage;
use Famoser\PolyasVerification\Workflow\ApiClient;
use Famoser\PolyasVerification\Workflow\DownloadReceipt;
use Famoser\PolyasVerification\Workflow\ExportReceipts;
use Famoser\PolyasVerification\Workflow\StoreReceipt;
use Famoser\PolyasVerification\Workflow\Verification;
use Famoser\PolyasVerification\Workflow\VerifyReceipt;
use PHPUnit\Framework\TestCase;

class VerificationTest extends TestCase
{
    public function testReceiptVerify(): void
    {
        $input = json_decode(file_get_contents(__DIR__.'/resources/ballot0/0_QRcode.json'), true); // @phpstan-ignore-line
        $deviceParametersJson = file_get_contents(__DIR__.'/resources/ballot0/deviceParameters.json');

        $apiClient = \Mockery::mock(ApiClient::class);
        $loginRequest = json_decode(file_get_contents(__DIR__.'/resources/ballot0/1_LoginRequest.json'), true); // @phpstan-ignore-line
        $loginResponse = json_decode(file_get_contents(__DIR__.'/resources/ballot0/2_LoginResponse.json'), true); // @phpstan-ignore-line
        $apiClient->shouldReceive('postLogin')->with($loginRequest)->andReturn($loginResponse); // @phpstan-ignore-line

        $challengeRequest = json_decode(file_get_contents(__DIR__.'/resources/ballot0/3_ChallengeRequest.json'), true); // @phpstan-ignore-line
        $challengeResponse = json_decode(file_get_contents(__DIR__.'/resources/ballot0/4_ChallengeResponse.json'), true); // @phpstan-ignore-line
        $token = 'MDIwNWJmMmUxNDQ5NmY2OGMwZjg2ZjZiMzEzZjIxMGE5MzkzZWRiMDgzODIxZGNjNGY5OTE0Y2FiOWM1MWM5ZjJl.UjVXYXRxTlRzdk12QWRwOA==';
        $apiClient->shouldReceive('postChallenge')->with($challengeRequest, $token)->andReturn($challengeResponse); // @phpstan-ignore-line

        $challenge = gmp_init($challengeRequest['challenge'], 10);
        $challengeRandomCoin = gmp_init($challengeRequest['challengeRandomCoin'], 10);
        $commit = new ChallengeCommit($challenge, $challengeRandomCoin);

        $verification = new Verification($deviceParametersJson, $apiClient);  // @phpstan-ignore-line
        $validationResult = $verification->verify($input, $commit, $error, $validReceipt);
        $this->assertNull($error);
        $this->assertEquals($validationResult, '00000001');

        // store receipt
        Storage::resetDb();
        $verificationKey = json_decode($deviceParametersJson, true)['verificationKey']; // @phpstan-ignore-line
        $storeReceipt = new StoreReceipt($verificationKey, 'electionId');
        $storeResult = $storeReceipt->store($validReceipt, $storeError);
        $this->assertNull($storeError);
        $this->assertTrue($storeResult);

        // download receipt
        $storeReceipt = new DownloadReceipt($verificationKey, 'electionId');
        $storeResult = $storeReceipt->store($validReceipt, $pdf, $storeError);
        $this->assertNull($storeError);
        $this->assertNotNull($pdf);
        $this->assertTrue($storeResult);

        // verify receipt
        $path = 'pdf.pdf';
        file_put_contents($path, $pdf);
        $receipt = new VerifyReceipt($verificationKey);
        $result = $receipt->verify($path, $failedCheck);
        $this->assertTrue($result);
        $this->assertNull($failedCheck);
        unlink($path);

        // export receipt
        $exportReceipt = new ExportReceipts('electionId');
        $exportReceipt->exportAll($pdfs, $exportError);
        $this->assertNull($exportError);
        $this->assertNotNull($pdfs);
        $this->assertCount(1, $pdfs);

        // check content is equal up to the creation date
        $creationDatePattern = '#/CreationDate \(D:[0-9]+\+00\'00\)#';
        $originalPDF = preg_replace($creationDatePattern, '', $pdf);
        $exportedPDF = preg_replace($creationDatePattern, '', $pdfs[0]);
        $this->assertEquals($originalPDF, $exportedPDF);
    }
}
