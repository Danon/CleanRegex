<?php
namespace Test\Feature\TRegx\CleanRegex\Match\Details\toInt;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Exception\IntegerFormatException;
use TRegx\CleanRegex\Match\Details\Match;

class MatchImplTest extends TestCase
{
    /**
     * @test
     * @dataProvider validIntegers
     * @param string $string
     * @param int $expected
     */
    public function shouldDelegate_first_detailsToInt(string $string, int $expected)
    {
        // given
        $result = pattern('-?\w+')
            ->match($string)
            ->first(function (Match $match) {
                // when
                return $match->toInt();
            });

        // then
        $this->assertEquals($expected, $result);
    }

    public function validIntegers(): array
    {
        return [
            ['1', 1],
            ['-1', -1],
            ['0', 0],
            ['000', 0],
            ['011', 11],
            ['0001', 1],
        ];
    }

    /**
     * @test
     */
    public function shouldThrow_first_forPseudoIntegerBecausePhpSucks()
    {
        // then
        $this->expectException(IntegerFormatException::class);
        $this->expectExceptionMessage("Expected to parse '1e3', but it is not a valid integer");

        // given
        pattern('.*', 's')
            ->match('1e3')
            ->first(function (Match $match) {
                // when
                return $match->toInt();
            });
    }

    /**
     * @test
     */
    public function shouldThrow_first_forInvalidInteger()
    {
        // then
        $this->expectException(IntegerFormatException::class);
        $this->expectExceptionMessage("Expected to parse 'Foo', but it is not a valid integer");

        // given
        pattern('\w+')
            ->match('Foo bar')
            ->first(function (Match $match) {
                // when
                return $match->toInt();
            });
    }
}
