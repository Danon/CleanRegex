<?php
namespace Test\Feature\TRegx\CleanRegex\Match\groupBy;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Match\AbstractMatchPattern;
use TRegx\CleanRegex\Match\Details\Match;
use TRegx\CleanRegex\Match\GroupByPattern;

class MatchPatternTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturn_group_byName_texts()
    {
        // when
        $result = $this->groupBy()->texts();

        // then
        $this->assertEquals([
            'cm' => ['14cm', '19cm', '2cm'],
            'mm' => ['13mm', '18mm']
        ], $result);
    }

    /**
     * @test
     */
    public function shouldReturn_group_byName_offsets()
    {
        // when
        $result = $this->groupBy()->offsets();

        // then
        $this->assertEquals([
            'cm' => [5, 15, 25],
            'mm' => [10, 20],
        ], $result);
    }

    /**
     * @test
     */
    public function shouldReturn_group_byName_byteOffsets()
    {
        // when
        $result = $this->groupBy()->byteOffsets();

        // then
        $this->assertEquals([
            'cm' => [7, 17, 27],
            'mm' => [12, 22],
        ], $result);
    }

    /**
     * @test
     */
    public function shouldReturn_group_byName_map()
    {
        // when
        $result = $this->groupBy()->map(function (Match $match) {
            return "$match";
        });

        // then
        $this->assertEquals([
            'cm' => ['14cm', '19cm', '2cm'],
            'mm' => ['13mm', '18mm'],
        ], $result);
    }

    /**
     * @test
     */
    public function shouldReturn_group_byName_flatMap()
    {
        // when
        $result = $this->groupBy()->flatMap(function (Match $match) {
            return ["$match", $match->offset()];
        });

        // then
        $this->assertEquals([
            'cm' => ['14cm', 5, '19cm', 15, '2cm', 25],
            'mm' => ['13mm', 10, '18mm', 20],
        ], $result);
    }

    /**
     * @test
     */
    public function shouldReturn_filter_group_byName_texts()
    {
        // given
        $groupByPattern = $this->filtered();

        // when
        $result = $groupByPattern->texts();

        // then
        $this->assertEquals([
            'cm' => ['19cm', '2cm'],
            'mm' => ['18mm'],
        ], $result);
    }

    /**
     * @test
     * @dataProvider mappersWithMatch
     * @param string $function
     * @param array $expected
     */
    public function shouldReturn_filter_group_byName_mixed(string $function, array $expected)
    {
        // given
        $groupByPattern = $this->filtered();

        // when
        $result = $groupByPattern->$function(function (Match $match) {
            return [$match->text(), $match->offset()];
        });

        // then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider mappersWithMatch
     * @param string $function
     */
    public function shouldReceive_filter_group_byName_mixed_detailsLimit(string $function)
    {
        // given
        $groupBy = $this->filtered();
        $called = 0;

        // when
        $groupBy->$function(function (Match $match) use (&$called) {
            // when
            $called++;
            $this->assertEquals(-1, $match->limit());

            // clean
            return [];
        });

        // then
        $this->assertEquals(3, $called, "Failed to assert that $function() was called 3 times");
        // There are 6 entries: '12' doesn't have 'unit' group matched, '14cm' and '13mm' are filtered out, 3 are left
    }

    /**
     * @test
     * @dataProvider mappersWithMatch
     * @param string $function
     */
    public function shouldReturn_filter_group_byName_mixed_detailsAll(string $function)
    {
        // given
        $groupByPattern = $this->filtered();

        // when
        $groupByPattern->$function(function (Match $match) {
            // when
            $this->assertEquals(['12', '19cm', '18mm', '2cm'], $match->all());

            // clean
            return [];
        });
    }

    /**
     * @test
     * @dataProvider mappersWithMatch
     * @param string $function
     */
    public function shouldPreserveUserData_filter_group_byName_mixed(string $function)
    {
        // given
        $groupByPattern = $this->filtered();

        // when
        $groupByPattern->$function(function (Match $match) {
            // when
            $this->assertEquals("verify me:$match", $match->getUserData());

            // clean
            return [];
        });
    }

    /**
     * @test
     * @dataProvider mappersWithMatch
     * @param string $function
     */
    public function shouldReceive_group_byName_mixed_detailsIndex(string $function)
    {
        // given
        $groupByPattern = $this->groupBy();
        $indexes = [];

        // when
        $groupByPattern->$function(function (Match $match) use (&$indexes) {
            // when
            $indexes[$match->text()] = $match->index();

            // clean
            return [];
        });

        // then
        $this->assertEquals(['14cm' => 1, '13mm' => 2, '19cm' => 3, '18mm' => 4, '2cm' => 5], $indexes);
    }

    /**
     * @test
     * @dataProvider mappersWithMatch
     * @param string $function
     */
    public function shouldReceive_filter_group_byName_mixed_detailsIndex(string $function)
    {
        // given
        $groupByPattern = $this->filtered();
        $indexes = [];

        // when
        $groupByPattern->$function(function (Match $match) use (&$indexes) {
            // when
            $indexes[$match->text()] = $match->index();

            // clean
            return [];
        });

        // then
        $this->assertEquals(['19cm' => 1, '18mm' => 2, '2cm' => 3], $indexes);
    }

    public function mappersWithMatch(): array
    {
        return [
            'map'     => ['map', [
                'cm' => [['19cm', 15], ['2cm', 25]],
                'mm' => [['18mm', 20]],
            ]],
            'flatMap' => ['flatMap', [
                'cm' => ['19cm', 15, '2cm', 25],
                'mm' => ['18mm', 20],
            ]],
        ];
    }

    private function groupBy(): GroupByPattern
    {
        return $this->match()->groupBy('unit');
    }

    private function filtered(): GroupByPattern
    {
        return $this
            ->match()
            ->filter(function (Match $match) {
                $match->setUserData("verify me:$match");
                return !in_array($match->text(), ['14cm', '13mm']);
            })
            ->groupBy('unit');
    }

    private function match(): AbstractMatchPattern
    {
        return pattern('\d+(?<unit>cm|mm)?')->match('€12, 14cm 13mm 19cm 18mm 2cm');
    }
}
