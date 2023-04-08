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
        $errors = self::readErrors();
        $remainingErrors = array_diff($errors, $whitelist);
        if (count($remainingErrors) > 0) {
            throw new self($remainingErrors);
        }
    }

    public static function createWithErrors(string $message): OpenSSLException
    {
        $errors = self::readErrors();
        $errors[] = $message;

        return new self($errors);
    }

    /**
     * @return string[]
     */
    private static function readErrors(): array
    {
        $errors = [];
        while ($error = openssl_error_string()) {
            $errors[] = $error;
        }

        return $errors;
    }
}
