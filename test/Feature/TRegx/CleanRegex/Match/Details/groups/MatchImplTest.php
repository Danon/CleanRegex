<?php
namespace Test\Feature\TRegx\CleanRegex\Match\Details\groups;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Match\Details\Match;

class MatchImplTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDelegate_map_detailsGroupsTexts_forEmptyString()
    {
        // when
        $groups = pattern('([a-z]+)(?:\((\d*)\))?')
            ->match('sin(20) + cos() + tan')
            ->map(function (Match $match) {
                return $match->groups()->texts();
            });

        // then
        $expected = [
            ['sin', '20'], // braces value
            ['cos', ''],   // empty braces
            ['tan', null], // no braces
        ];
        $this->assertEquals($expected, $groups);
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsNamedGroupsTexts()
    {
        // given
        pattern('(?<one>first) and (?<two>second)')
            ->match('first and second')
            ->first(function (Match $match) {
                // when
                $groupNames = $match->namedGroups()->texts();

                // then
                $expected = [
                    'one' => 'first',
                    'two' => 'second'
                ];
                $this->assertEquals($expected, $groupNames);
            });
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsGroupsOffsets_batch()
    {
        // given
        pattern('(?<one>first ę) and (?<two>second)')
            ->match('first ę and second')
            ->first(function (Match $match) {
                // when
                $offsets = $match->groups()->offsets();
                $byteOffsets = $match->groups()->byteOffsets();

                // then
                $this->assertEquals([0, 12], $offsets);
                $this->assertEquals([0, 13], $byteOffsets);
            });
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsNamedGroupsOffsets_batch()
    {
        // given
        pattern('(?<one>first ę) and (?<two>second)')
            ->match('first ę and second')
            ->first(function (Match $match) {
                // when
                $offsets = $match->namedGroups()->offsets();
                $byteOffsets = $match->namedGroups()->byteOffsets();

                // then
                $this->assertEquals(['one' => 0, 'two' => 12], $offsets);
                $this->assertEquals(['one' => 0, 'two' => 13], $byteOffsets);
            });
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsGroupNames()
    {
        // given
        pattern('(?<one>first) (and) (?<two>second)')
            ->match('first and second')
            ->first(function (Match $match) {
                // when
                $groupNames = $match->groupNames();

                // then
                $this->assertEquals(['one', null, 'two'], $groupNames);
            });
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsGroupsCount()
    {
        // given
        pattern('(?<one>first) and (second)')
            ->match('first and second')
            ->first(function (Match $match) {
                // when
                $groupsCount = $match->groupsCount();

                // then
                $this->assertEquals(2, $groupsCount);
            });
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsHasGroup_forNonexistentGroup()
    {
        // given
        pattern('(?<one>first) and (?<two>second)')
            ->match('first and second')
            ->first(function (Match $match) {
                // when
                $has = $match->hasGroup('nonexistent');

                // then
                $this->assertFalse($has);
            });
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsHasGroup()
    {
        // given
        pattern('(?<existing>first) and (?<two_existing>second)')
            ->match('first and second')
            ->first(function (Match $match) {
                // when
                $has = $match->hasGroup('existing');

                // then
                $this->assertTrue($has);
            });
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsGroupsNames_batch()
    {
        // given
        pattern('(zero) (?<existing>first) and (?<two_existing>second)')
            ->match('zero first and second')
            ->first(function (Match $match) {
                // when
                $groupNames = $match->groups()->names();
                $namedGroups = $match->namedGroups()->names();

                // then
                $this->assertEquals([null, 'existing', 'two_existing'], $groupNames);
                $this->assertEquals(['existing', 'two_existing'], $namedGroups);
            });
    }

    /**
     * @test
     */
    public function shouldReceive_first_detailsGroupsCount_batch()
    {
        // given
        pattern('(zero) (?<existing>first) and (?<two_existing>second)')
            ->match('zero first and second')
            ->first(function (Match $match) {
                // when
                $groups = $match->groups()->count();
                $namedGroups = $match->namedGroups()->count();

                // then
                $this->assertEquals(3, $groups);
                $this->assertEquals(2, $namedGroups);
            });
    }

    /**
     * @test
     */
    public function shouldThrow_first_detailsHasGroup_forInvalidGroup()
    {
        // then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Group name must be an alphanumeric string starting with a letter, given: '2sd'");

        // given
        pattern('(?<one>first) and (?<two>second)')
            ->match('first and second')
            ->first(function (Match $match) {
                // when
                $match->hasGroup('2sd');
            });
    }
}
