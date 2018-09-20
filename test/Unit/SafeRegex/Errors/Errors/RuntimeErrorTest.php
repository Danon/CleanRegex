<?php
namespace Test\Unit\SafeRegex\Errors\Errors;

use CleanRegex\Exception\CleanRegex\InternalCleanRegexException;
use PHPUnit\Framework\TestCase;
use SafeRegex\Errors\Errors\RuntimeError;
use SafeRegex\Exception\RuntimeSafeRegexException;
use Test\Warnings;

class RuntimeErrorTest extends TestCase
{
    use Warnings;

    /**
     * @test
     */
    public function shouldOccur()
    {
        // given
        $error = new RuntimeError(PREG_BACKTRACK_LIMIT_ERROR);

        // when
        $occurred = $error->occurred();

        // then
        $this->assertTrue($occurred);
    }

    /**
     * @test
     */
    public function shouldNotOccur()
    {
        // given
        $error = new RuntimeError(PREG_NO_ERROR);

        // when
        $occurred = $error->occurred();

        // then
        $this->assertFalse($occurred);
    }

    /**
     * @test
     */
    public function shouldGetLast()
    {
        // given
        $this->causeRuntimeWarning();

        // when
        $error = RuntimeError::getLast();

        // then
        $this->assertTrue($error->occurred());

        // cleanup
        $error->clear();
    }

    /**
     * @test
     */
    public function shouldNotGetLast()
    {
        // when
        $error = RuntimeError::getLast();

        // then
        $this->assertFalse($error->occurred());
    }

    /**
     * @test
     */
    public function shouldClean()
    {
        // given
        $this->causeRuntimeWarning();
        $error = RuntimeError::getLast();

        // when
        $error->clear();

        // then
        $this->assertFalse(RuntimeError::getLast()->occurred());
    }

    /**
     * @test
     */
    public function shouldGetSafeRegexException()
    {
        // given
        $error = new RuntimeError(PREG_BAD_UTF8_ERROR);

        // when
        /** @var RuntimeSafeRegexException $exception */
        $exception = $error->getSafeRegexpException('preg_replace');

        // then
        $this->assertInstanceOf(RuntimeSafeRegexException::class, $exception);
        $this->assertEquals('preg_replace', $exception->getInvokingMethod());
        $this->assertEquals(PREG_BAD_UTF8_ERROR, $exception->getError());
        $this->assertEquals('PREG_BAD_UTF8_ERROR', $exception->getErrorName());
        $this->assertEquals('preg_replace', $exception->getInvokingMethod());
        $this->assertEquals("After invoking preg_replace(), preg_last_error() returned PREG_BAD_UTF8_ERROR.",
            $exception->getMessage());
    }

    /**
     * @test
     */
    public function shouldNotGetSafeRegexException()
    {
        // given
        $error = new RuntimeError(PREG_NO_ERROR);

        // then
        $this->expectException(InternalCleanRegexException::class);

        // when
        $error->getSafeRegexpException('preg_match');
    }
}
