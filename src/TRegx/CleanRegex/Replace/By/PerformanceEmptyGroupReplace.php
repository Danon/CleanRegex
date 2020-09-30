<?php
namespace TRegx\CleanRegex\Replace\By;

use TRegx\CleanRegex\Internal\InternalPattern;
use TRegx\CleanRegex\Internal\Subjectable;
use TRegx\SafeRegex\preg;

class PerformanceEmptyGroupReplace
{
    /** @var InternalPattern */
    private $pattern;
    /** @var Subjectable */
    private $subject;
    /** @var int */
    private $limit;

    public function __construct(InternalPattern $pattern, Subjectable $subject, int $limit)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->limit = $limit;
    }

    public function replaceWithGroupOrEmpty(int $index): string
    {
        /**
         * T-Regx provides 4 strategies, when replacing occurrence with a group
         * that is unmatched: ignore it, leave it empty, invoke callback or throw.
         *
         * Ignoring, calling back or throwing requires `preg_replace_callback()`.
         * However, replacing a group that's indexed with an empty string, is possible
         * with just preg_replace().
         */
        return preg::replace(
            $this->pattern->pattern,
            "\\$index",
            $this->subject->getSubject(),
            $this->limit);
    }
}
