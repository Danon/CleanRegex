<?php
namespace Test;

use PHPUnit\Framework\TestCase;

/**
 * Remove this class when PhpUnit7 is not longer used by T-Regx
 */
trait PhpunitPolyfill
{
    public function expectExceptionMessageMatches(string $message): void
    {
        if (!method_exists(TestCase::class, 'expectExceptionMessageMatches')) {
            parent::expectExceptionMessageRegExp($message);
        } else {
            parent::expectExceptionMessageMatches($message);
        }
    }
}