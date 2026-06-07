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
use Famoser\PolyasVerification\Storage;
use Famoser\PolyasVerification\Test\resources\ballot0\Ballot0;
use Famoser\PolyasVerification\Workflow\ApiClient;
use Famoser\PolyasVerification\Workflow\DownloadReceipt;
use Famoser\PolyasVerification\Workflow\ExportReceipts;
use Famoser\PolyasVerification\Workflow\Verification;
use Famoser\PolyasVerification\Workflow\VerifyReceipt;
use PHPUnit\Framework\TestCase;

class VerificationTest extends TestCase
{
    public function testReceiptVerify(): void
    {
        $election = 'electionId';
        $input = Ballot0::getQRCode();
        $deviceParameters = Ballot0::getDeviceParameters();

        $apiClient = \Mockery::mock(ApiClient::class);
        $loginRequest = Ballot0::getLoginRequest();
        $loginResponse = Ballot0::getLoginResponse();
        $apiClient->shouldReceive('postLogin')->with($loginRequest)->andReturn($loginResponse); // @phpstan-ignore-line

        $challengeRequest = Ballot0::getChallengeRequest();
        $challengeResponse = Ballot0::getChallengeResponse();
        $token = $loginResponse['value']['token'];
        $apiClient->shouldReceive('postChallenge')->with($challengeRequest, $token)->andReturn($challengeResponse); // @phpstan-ignore-line
        /** @var ApiClient $apiClient */

        $challenge = gmp_init($challengeRequest['challenge'], 10);
        $challengeRandomCoin = gmp_init($challengeRequest['challengeRandomCoin'], 10);
        $commit = new ChallengeCommit($challenge, $challengeRandomCoin);

        Storage::resetDb();
        $verification = new Verification($deviceParameters, $apiClient, $election);
        $status = $verification->verify($input, $loginRequest['password'], $commit, $validReceipt, $hexBallot, $error);
        $this->assertTrue($status);
        $this->assertNull($error);
        $this->assertEquals('00000100', $hexBallot);
        $this->assertTrue(Storage::checkReceiptExists($validReceipt));

        // download receipt
        $storeReceipt = new DownloadReceipt($deviceParameters->getVerificationKey(), 'electionId');
        $storeResult = $storeReceipt->store($validReceipt, $pdf, $storeError);
        $this->assertNull($storeError);
        $this->assertNotNull($pdf);
        $this->assertTrue($storeResult);

        // verify receipt
        $path = 'pdf.pdf';
        file_put_contents($path, $pdf);
        $receipt = new VerifyReceipt($deviceParameters->getVerificationKey());
        $status = $receipt->verify($path, $validReceipt, $failedCheck);
        $this->assertTrue($status);
        $this->assertNull($failedCheck);
        $this->assertNotNull($validReceipt);
        unlink($path);

        // export receipt
        $exportReceipt = new ExportReceipts($election);
        $status = $exportReceipt->exportAll($pdfs, $exportError);
        $this->assertTrue($status);
        $this->assertNull($exportError);
        $this->assertNotNull($pdfs);
        $this->assertCount(1, $pdfs);

        // check content is equal up to the creation date & the document id
        $creationDatePattern = '#/CreationDate \(D:[0-9]+\+00\'00\)#';
        $originalPDF = preg_replace($creationDatePattern, '', $pdf) ?? "";
        $exportedPDF = preg_replace($creationDatePattern, '', $pdfs[array_key_first($pdfs)]) ?? "";
        $documentIdPattern = '#<xmpMM:(InstanceID|DocumentID)>[a-zA-Z0-9]+</xmpMM:(InstanceID|DocumentID)>#';
        $originalPDF = preg_replace($documentIdPattern, '', $originalPDF);
        $exportedPDF = preg_replace($documentIdPattern, '', $exportedPDF);
        $this->assertEquals($originalPDF, $exportedPDF);
    }
}
