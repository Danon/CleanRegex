<?php
namespace Test\Unit\SafeRegex;

use PHPUnit\Framework\TestCase;
use SafeRegex\Errors\ErrorsCleaner;
use SafeRegex\Exception\CompileSafeRegexException;
use SafeRegex\Exception\RuntimeSafeRegexException;
use SafeRegex\Exception\SuspectedReturnSafeRegexException;
use SafeRegex\ExceptionFactory;

class ExceptionFactoryTest extends TestCase
{
    protected function setUp()
    {
        (new ErrorsCleaner())->clear();
    }

    /**
     * @test
     * @dataProvider \Test\DataProviders::invalidPregPatterns()
     * @param string $invalidPattern
     */
    public function testCompileErrors(string $invalidPattern)
    {
        // given
        @preg_match($invalidPattern, '');

        // when
        $exception = (new ExceptionFactory())->retrieveGlobals('preg_match', false);

        // then
        $this->assertInstanceOf(CompileSafeRegexException::class, $exception);
    }

    /**
     * @test
     * @dataProvider \Test\DataProviders::invalidUtf8Sequences()
     * @param $description
     * @param $utf8
     */
    public function testRuntimeErrors(string $description, string $utf8)
    {
        // given
        @preg_match("/pattern/u", $utf8);

        // when
        $exception = (new ExceptionFactory())->retrieveGlobals('preg_match', false);

        // then
        $this->assertInstanceOf(RuntimeSafeRegexException::class, $exception);
    }

    /**
     * @test
     */
    public function testUnexpectedReturnError()
    {
        // given
        $result = false;

        // when
        $exception = (new ExceptionFactory())->retrieveGlobals('preg_match', $result);

        // then
        $this->assertInstanceOf(SuspectedReturnSafeRegexException::class, $exception);
        $this->assertEquals("Invoking preg_match() resulted in 'false'.", $exception->getMessage());
    }
}
