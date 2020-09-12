<?php
namespace Test\Feature\TRegx\CleanRegex\Match\group\findFirst;

use PHPUnit\Framework\TestCase;
use Test\Utils\CustomSubjectException;
use Test\Utils\Functions;
use TRegx\CleanRegex\Exception\GroupNotMatchedException;
use TRegx\CleanRegex\Exception\NonexistentGroupException;
use TRegx\CleanRegex\Exception\SubjectNotMatchedException;
use TRegx\CleanRegex\Match\Details\Group\MatchGroup;
use TRegx\CleanRegex\Match\Details\NotMatched;

class MatchPatternTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDelegate_group_findFirst_orThrow()
    {
        // when
        $result = pattern('Computer')
            ->match('Computer')
            ->group(0)
            ->findFirst(Functions::constant('result'))
            ->orThrow();

        // then
        $this->assertEquals('result', $result);
    }

    /**
     * @test
     */
    public function shouldReceive_group_findFirst_orThrow_detailsText()
    {
        // when
        pattern('[A-Z](?<lowercase>[a-z]+)?')
            ->match('Computer L Three Four')
            ->group('lowercase')
            ->findFirst(function (MatchGroup $group) {
                $this->assertEquals('omputer', $group->text());
            })
            ->orThrow();
    }

    /**
     * @test
     */
    public function shouldReceive_group_findFirst_orThrow_detailsAll()
    {
        // when
        pattern('[A-Z](?<lowercase>[a-z]+)?')
            ->match('Computer L Three Four')
            ->group('lowercase')
            ->findFirst(function (MatchGroup $group) {
                $this->assertEquals(['omputer', null, 'hree', 'our'], $group->all());
            })
            ->orThrow();
    }

    /**
     * @test
     */
    public function shouldReceive_group_findFirst_orThrow_detailsText_forEmptyGroup()
    {
        // when
        pattern('Foo (?<bar>[a-z]*)')
            ->match('Foo NOT MATCH')
            ->group('bar')
            ->findFirst(function (MatchGroup $group) {
                $this->assertEquals('', $group->text());
            })
            ->orThrow();
    }

    /**
     * @test
     */
    public function shouldThrow_group_findFirst_orThrow_onUnmatchedSubject()
    {
        // then
        $this->expectException(SubjectNotMatchedException::class);
        $this->expectExceptionMessage("Expected to get group '0' from the first match, but subject was not matched at all");

        // when
        pattern('Foo')
            ->match('123')
            ->group(0)
            ->findFirst(Functions::fail())
            ->orThrow();
    }

    /**
     * @test
     */
    public function shouldThrow_group_findFirst_orThrow_forUnmatchedGroup()
    {
        // then
        $this->expectException(GroupNotMatchedException::class);
        $this->expectExceptionMessage("Expected to get group 'group' from the first match, but the group was not matched");

        // when
        pattern('Foo(?<group>Bar)?')
            ->match('Foo')
            ->group('group')
            ->findFirst(Functions::fail())
            ->orThrow();
    }

    /**
     * @test
     */
    public function shouldThrow_group_findFirst_orThrow_onUnmatchedSubject_customException()
    {
        // then
        $this->expectException(CustomSubjectException::class);
        $this->expectExceptionMessage("Expected to get group '0' from the first match, but subject was not matched at all");

        // when
        pattern('Foo')
            ->match('123')
            ->group(0)
            ->findFirst(Functions::fail())
            ->orThrow(CustomSubjectException::class);
    }

    /**
     * @test
     */
    public function shouldThrow_group_findFirst_orThrow_forUnmatchedGroup_customException()
    {
        // then
        $this->expectException(CustomSubjectException::class);
        $this->expectExceptionMessage("Expected to get group 'lowercase' from the first match, but the group was not matched");

        // when
        pattern('[A-Z](?<lowercase>[a-z]+)?')
            ->match('L')
            ->group('lowercase')
            ->findFirst(Functions::fail())
            ->orThrow(CustomSubjectException::class);
    }

    /**
     * @test
     */
    public function shouldPass_group_findFirst_orElse_NotMatched_onUnmatchedSubject()
    {
        // when
        pattern('Foo(?<one>)(?<two>)')
            ->match('123')
            ->group(0)
            ->findFirst(Functions::fail())
            ->orElse(function (NotMatched $notMatched) {
                $this->assertEquals(['one', 'two'], $notMatched->groupNames());
            });
    }

    /**
     * @test
     */
    public function shouldPass_group_findFirst_orElse_NotMatched_forUnmatchedGroup()
    {
        // when
        pattern('Foo(?<one>Bar)?')
            ->match('Foo')
            ->group(1)
            ->findFirst(Functions::fail())
            ->orElse(function (NotMatched $notMatched) {
                $this->assertEquals(['one'], $notMatched->groupNames());
            });
    }

    /**
     * @test
     */
    public function shouldThrow_group_findFirst_orThrow_forNonexistentGroup()
    {
        // given
        $subject = 'L Three Four';

        // then
        $this->expectException(NonexistentGroupException::class);
        $this->expectExceptionMessage("Nonexistent group: 'missing'");

        // when
        pattern('[A-Z](?<lowercase>[a-z]+)?')->match($subject)
            ->group('missing')
            ->findFirst(Functions::fail())
            ->orReturn('');
    }
}
