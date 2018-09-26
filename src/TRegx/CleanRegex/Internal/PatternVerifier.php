<?php
namespace TRegx\CleanRegex\Internal;

use Throwable;
use TRegx\CleanRegex\Exception\CleanRegex\InvalidPatternException;
use TRegx\SafeRegex\Exception\CompileSafeRegexException;
use TRegx\SafeRegex\preg;

class PatternVerifier
{
    /** @var string */
    private $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function verify(): void
    {
        try {
            preg::match($this->pattern, '');
        } catch (CompileSafeRegexException $exception) {
            $message = $this->getMessage($exception);
            throw new InvalidPatternException($message, $exception);
        }
    }

    private function getMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();
        if (preg::match('/preg_[a-z_]+\(\): (.*)/A', $message, $matches) === 1) {
            return trim($matches[1]);
        }
        return $message;
    }
}
