<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PolyasVerification\Crypto\RSA;

class OpenSSLException extends \RuntimeException
{
    /**
     * @param string[] $errors
     */
    private function __construct(array $errors)
    {
        parent::__construct(implode("\n", $errors));
    }

    public static function throwIfErrors(string ...$whitelist): void
    {
        $errors = self::readErrors($whitelist);
        if (count($errors) > 0) {
            throw new self($errors);
        }
    }

    public static function createWithErrors(string $message, string ...$whitelist): OpenSSLException
    {
        $errors = self::readErrors($whitelist);
        $errors[] = $message;

        return new self($errors);
    }

    /**
     * @param string[] $whitelist
     *
     * @return string[]
     */
    private static function readErrors(array $whitelist): array
    {
        $errors = [];
        while ($error = openssl_error_string()) {
            $errors[] = $error;
        }

        return array_diff($errors, $whitelist);
    }
}
