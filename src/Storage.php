<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification;

use Slim\Psr7\UploadedFile;

class Storage
{
    private const DB_PATH = PathHelper::VAR_PERSISTENT_DIR.DIRECTORY_SEPARATOR.'receipts.sqlite';
    private const VERSION_PATH = self::DB_PATH.'.version';

    public static function writeUploadedFile(string $dir, UploadedFile $file): string
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = uniqid().'.pdf';
        $path = $dir.DIRECTORY_SEPARATOR.$filename;

        $file->moveTo($path);

        return $path;
    }

    /**
     * @throws \Exception
     */
    public static function readFile(string $path): string
    {
        $content = file_get_contents($path);
        if (!$content) {
            throw new \Exception('File not found');
        }

        return $content;
    }

    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public static function readJsonFile(string $path): array
    {
        $content = self::readFile($path);

        return json_decode($content, true);
    }

    public static function removeFile(string $path): void
    {
        unlink($path);
    }

    /**
     * @param array{
     *  'fingerprint': string,
     *  'signature': string,
     *  } $payload
     */
    public static function checkReceiptExists(array $payload): bool
    {
        $db = self::getDatabaseConnection();

        $smt = $db->prepare('SELECT * FROM receipts WHERE fingerprint = :fingerprint AND signature = :signature');
        $smt->bindValue(':fingerprint', $payload['fingerprint']);
        $smt->bindValue(':signature', $payload['signature']);
        $smt->execute();

        $results = $smt->fetchAll();

        return count($results) > 0;
    }

    /**
     * @param array{
     *  'fingerprint': string,
     *   'signature': string,
     *   'ballotVoterId': ?string,
     *  } $payload
     */
    public static function storeReceipt(array $payload, string $electionId): bool
    {
        $db = self::getDatabaseConnection();

        $smt = $db->prepare('INSERT INTO receipts (fingerprint, signature, ballot_voter_id, election_id) VALUES (:fingerprint, :signature, :ballot_voter_id, :election_id)');
        $smt->bindValue(':fingerprint', $payload['fingerprint']);
        $smt->bindValue(':signature', $payload['signature']);
        $smt->bindValue(':ballot_voter_id', $payload['ballotVoterId'] ?? null);
        $smt->bindValue(':election_id', $electionId);

        return $smt->execute();
    }

    /**
     * @return array{array{
     *  'fingerprint': string,
     *   'signature': string,
     *   'ballotVoterId': ?string,
     *  }} $payload
     */
    public static function getReceipts(string $electionId): array
    {
        $db = self::getDatabaseConnection();

        $smt = $db->prepare('SELECT fingerprint, signature, ballot_voter_id as ballotVoterId FROM receipts WHERE election_id = :election_id OR election_id = NULL');
        $smt->bindValue(':election_id', $electionId);
        $smt->execute();

        return $smt->fetchAll(); // @phpstan-ignore-line
    }

    private static \PDO|null $pdo = null;

    private static function getDatabaseConnection(): \PDO
    {
        if (!self::$pdo) {
            $dbExists = file_exists(self::DB_PATH);
            $version = file_exists(self::VERSION_PATH) ? file_get_contents(self::VERSION_PATH) : '';

            self::$pdo = new \PDO('sqlite:'.self::DB_PATH);
            if (!$dbExists) {
                self::$pdo->exec('CREATE TABLE receipts (fingerprint TEXT NOT NULL, signature TEXT NOT NULL, UNIQUE(fingerprint,signature))');
            }

            if ('' === $version) {
                self::$pdo->exec('ALTER TABLE receipts ADD ballot_voter_id TEXT');
                self::$pdo->exec('ALTER TABLE receipts ADD election_id TEXT');
            }

            file_put_contents(self::VERSION_PATH, 'with_fingerprint_meta');
        }

        return self::$pdo;
    }

    public static function resetDb(): void
    {
        unlink(self::DB_PATH);
        unlink(self::VERSION_PATH);
    }
}
