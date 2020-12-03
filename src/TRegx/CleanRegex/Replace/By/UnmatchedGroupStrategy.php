<?php
namespace TRegx\CleanRegex\Replace\By;

use TRegx\CleanRegex\Exception\GroupNotMatchedException;
use TRegx\CleanRegex\Internal\Exception\Messages\Group\ReplacementWithUnmatchedGroupMessage;
use TRegx\CleanRegex\Internal\Replace\By\GroupFallbackReplacer;
use TRegx\CleanRegex\Internal\Replace\GroupMapper\GroupMapper;
use TRegx\CleanRegex\Internal\Replace\GroupMapper\Wrapper;
use TRegx\CleanRegex\Internal\Replace\GroupMapper\WrappingMatchRs;
use TRegx\CleanRegex\Internal\Replace\NonReplaced\ComputedMatchStrategy;
use TRegx\CleanRegex\Internal\Replace\NonReplaced\ConstantReturnStrategy;
use TRegx\CleanRegex\Internal\Replace\NonReplaced\DefaultStrategy;
use TRegx\CleanRegex\Internal\Replace\NonReplaced\MatchRs;
use TRegx\CleanRegex\Internal\Replace\NonReplaced\ThrowStrategy;
use TRegx\CleanRegex\Replace\GroupReplace;

class UnmatchedGroupStrategy implements GroupReplace
{
    /** @var GroupFallbackReplacer */
    private $replacer;
    /** @var string|int */
    private $nameOrIndex;
    /** @var GroupMapper */
    private $mapper;
    /** @var Wrapper */
    private $middlewareMapper;

    public function __construct(GroupFallbackReplacer $replacer, $nameOrIndex, GroupMapper $mapper, Wrapper $middlewareMapper)
    {
        $this->replacer = $replacer;
        $this->nameOrIndex = $nameOrIndex;
        $this->mapper = $mapper;
        $this->middlewareMapper = $middlewareMapper;
    }

    public function orElseWith(string $replacement): string
    {
        return $this->replace(new ConstantReturnStrategy($replacement));
    }

    public function orElseCalling(callable $replacementProducer): string
    {
        return $this->replace(new ComputedMatchStrategy($replacementProducer, "orElseCalling"));
    }

    public function orElseThrow(string $exceptionClassName = null): string
    {
        return $this->replace(new ThrowStrategy(
            $exceptionClassName ?? GroupNotMatchedException::class,
            new ReplacementWithUnmatchedGroupMessage($this->nameOrIndex)));
    }

    public function orElseIgnore(): string
    {
        return $this->replace(new DefaultStrategy());
    }

    public function orElseEmpty(): string
    {
        return $this->replace(new ConstantReturnStrategy(''));
    }

    private function replace(MatchRs $substitute): string
    {
        return $this->replacer->replaceOrFallback($this->nameOrIndex, $this->mapper, new WrappingMatchRs($substitute, $this->middlewareMapper));
    }
}
