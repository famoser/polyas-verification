<?php

namespace Famoser\PolyasVerification\Test\Utils;

trait IncompleteTestTrait
{
    protected function markTestIncompleteNS(string $message): void
    {
        if (time() > 1000) {
            $this->markTestIncomplete($message);
        }
    }
}