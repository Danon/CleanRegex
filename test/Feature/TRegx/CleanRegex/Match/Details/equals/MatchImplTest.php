<?php
namespace Test\Feature\TRegx\CleanRegex\Match\Details\equals;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Match\Details\Match;

class MatchImplTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReceive_findFirst_detailsGroupEquals_byIndex()
    {
        // given
        pattern('Foo(Bar)')->match('FooBar')->findFirst(function (Match $match) {
            $this->assertTrue($match->group(1)->equals('Bar'));
        });
    }

    /**
     * @test
     */
    public function shouldReceive_findFirst_detailsGroupEquals_byIndex_forUnequal()
    {
        // given
        pattern('Foo(Bar)')->match('FooBar')->findFirst(function (Match $match) {
            $this->assertFalse($match->group(1)->equals('something else'));
        });
    }

    /**
     * @test
     */
    public function shouldReceive_findFirst_detailsGroupEquals_byIndex_forUnmatchedGroup()
    {
        // given
        pattern('Foo(Bar)?')->match('Foo')->findFirst(function (Match $match) {
            $this->assertFalse($match->group(1)->equals('irrelevant'));
        });
    }
}
