<?php
namespace TRegx\CleanRegex\Internal\Replace\GroupMapper;

use TRegx\CleanRegex\Match\Details\Detail;

class WrappingMapper implements GroupMapper
{
    /** @var GroupMapper */
    private $groupMapper;
    /** @var Wrapper */
    private $mapperWrapper;

    public function __construct(GroupMapper $groupMapper, Wrapper $mapperWrapper)
    {
        $this->groupMapper = $groupMapper;
        $this->mapperWrapper = $mapperWrapper;
    }

    public function map(string $occurrence, Detail $initialDetail): ?string
    {
        return $this->mapperWrapper->wrap(new GroupMapperWrappable($this->groupMapper, $occurrence), $initialDetail);
    }

    public function useExceptionValues(string $occurrence, $nameOrIndex, string $match): void
    {
    }
}
