<?php
namespace TRegx\CleanRegex;

use TRegx\CleanRegex\Internal\CompositePatternMapper;
use TRegx\CleanRegex\Internal\Prepared\Parser\BindingParser;
use TRegx\CleanRegex\Internal\Prepared\Parser\InjectParser;
use TRegx\CleanRegex\Internal\Prepared\Parser\Parser;
use TRegx\CleanRegex\Internal\Prepared\Parser\PreparedParser;
use TRegx\CleanRegex\Internal\Prepared\PrepareFacade;

class PatternBuilder
{
    /**
     * @param string $input
     * @param string[] $values
     * @return Pattern
     */
    public static function bind(string $input, array $values): Pattern
    {
        return self::build(new BindingParser($input, $values));
    }

    /**
     * @param string $input
     * @param string[] $values
     * @return Pattern
     */
    public static function inject(string $input, array $values): Pattern
    {
        return self::build(new InjectParser($input, $values));
    }

    /**
     * @param (string|string[])[] $input
     * @return Pattern
     */
    public static function prepare(array $input): Pattern
    {
        return self::build(new PreparedParser($input));
    }

    /**
     * @param (string|Pattern)[] $patterns
     * @return CompositePattern
     */
    public static function compose(array $patterns): CompositePattern
    {
        return new CompositePattern((new CompositePatternMapper($patterns))->createPatterns());
    }

    private static function build(Parser $parser): Pattern
    {
        return new Pattern((new PrepareFacade($parser))->getPattern());
    }
}
