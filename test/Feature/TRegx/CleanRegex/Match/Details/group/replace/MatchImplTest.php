<?php
namespace Test\Feature\TRegx\CleanRegex\Match\Details\group\replace;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Match\Details\Match;

class MatchImplTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDelegate_map_detailsGroupReplace()
    {
        // given
        $result = pattern('https?://(?<domain>[\w.]+)/users')
            ->match('Link: http://facebook.com/users and https://google.com/users guys')
            ->map(function (Match $match) {
                // when
                return $match->group('domain')->replace('XD');
            });

        // then
        $this->assertEquals(['http://XD/users', 'https://XD/users'], $result);
    }

    /**
     * @test
     */
    public function shouldDelegate_first_detailsGroupReplace_forEmptyString()
    {
        // given
        $result = pattern('https?://(?<domain>([\w.]+)?)/users')
            ->match('Link: http:///users')
            ->first(function (Match $match) {
                // when
                return $match->group('domain')->replace('Welp');
            });

        // then
        $this->assertEquals('http://Welp/users', $result);
    }

    /**
     * @test
     */
    public function shouldDelegate_first_detailsGroupReplace_forStringUtf8()
    {
        // given
        $result = pattern('hłłps?://(?<domain>ąść)/users')
            ->match('Link: hłłp://ąść/users')
            ->first(function (Match $match) {
                // when
                return $match->group('domain')->replace('ś');
            });

        // then
        $this->assertEquals('hłłp://ś/users', $result);
    }
}
