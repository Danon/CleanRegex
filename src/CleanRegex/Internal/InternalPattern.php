<?php
namespace TRegx\CleanRegex\Internal;

class InternalPattern
{
    /** @var string */
    public $pattern;
    /** @var string */
    public $undevelopedInput;

    public function __construct(string $pattern, string $undevelopedInput)
    {
        $this->pattern = $pattern;
        $this->undevelopedInput = $undevelopedInput;
    }
}
