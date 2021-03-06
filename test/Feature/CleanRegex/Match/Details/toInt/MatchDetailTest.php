<?php
namespace Test\Feature\TRegx\CleanRegex\Match\Details\toInt;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Exception\IntegerFormatException;
use TRegx\CleanRegex\Match\Details\Detail;

class MatchDetailTest extends TestCase
{
    /**
     * @test
     * @dataProvider validIntegers
     * @param string $string
     * @param int $expected
     */
    public function shouldParseInt(string $string, int $expected)
    {
        // given
        $result = pattern('-?\d+')
            ->match($string)
            ->first(function (Detail $detail) {
                // when
                return $detail->toInt();
            });

        // then
        $this->assertSame($expected, $result);
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
    public function shouldThrow_forPseudoInteger_becausePhpSucks()
    {
        // then
        $this->expectException(IntegerFormatException::class);
        $this->expectExceptionMessage("Expected to parse '1e3', but it is not a valid integer");

        // given
        pattern('.*', 's')
            ->match('1e3')
            ->first(function (Detail $detail) {
                // when
                return $detail->toInt();
            });
    }

    /**
     * @test
     */
    public function shouldThrow_forInvalidInteger()
    {
        // then
        $this->expectException(IntegerFormatException::class);
        $this->expectExceptionMessage("Expected to parse 'Foo', but it is not a valid integer");

        // given
        pattern('Foo')->match('Foo')->first(function (Detail $detail) {
            // when
            return $detail->toInt();
        });
    }
}
