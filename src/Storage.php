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

        $smt = $db->prepare('SELECT FROM receipts WHERE fingerprint = :fingerprint AND signature = :signature');
        $smt->bindValue(':fingerprint', $payload['fingerprint']);
        $smt->bindValue(':signature', $payload['signature']);

        $results = $smt->fetchAll();

        return 0 === count($results);
    }

    /**
     * @param array{
     *  'fingerprint': string,
     *  'signature': string,
     *  } $payload
     */
    public static function storeReceipt(array $payload): bool
    {
        $db = self::getDatabaseConnection();

        $smt = $db->prepare("INSERT INTO receipts (fingerprint, signature) VALUES (':fingerprint', ':signature')");
        $smt->bindValue(':fingerprint', $payload['fingerprint']);
        $smt->bindValue(':signature', $payload['signature']);

        return $smt->execute();
    }

    private static \PDO|null $pdo;

    private static function getDatabaseConnection(): \PDO
    {
        if (!self::$pdo) {
            $dbPath = PathHelper::VAR_PERSISTENT_DIR.DIRECTORY_SEPARATOR.'receipts.sqlite';
            $dbExists = file_exists($dbPath);
            self::$pdo = new \PDO('sqlite:'.$dbPath);
            if (!$dbExists) {
                self::$pdo->exec('CREATE TABLE IF NOT EXISTS receipts (fingerprint TEXT NOT NULL, signature TEXT NOT NULL, UNIQUE(fingerprint,signature))');
            }
        }

        return self::$pdo;
    }
}
