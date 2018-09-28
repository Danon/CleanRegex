<?php
namespace TRegx\CleanRegex\Match\Details;

use TRegx\CleanRegex\Exception\CleanRegex\NonexistentGroupException;
use TRegx\CleanRegex\Internal\ByteOffset;
use TRegx\CleanRegex\Internal\Factory\Group\GroupFacade;
use TRegx\CleanRegex\Internal\Factory\Group\GroupFactoryStrategy;
use TRegx\CleanRegex\Internal\Factory\Group\MatchGroupFactoryStrategy;
use TRegx\CleanRegex\Internal\GroupNameIndexAssign;
use TRegx\CleanRegex\Internal\GroupNameValidator;
use TRegx\CleanRegex\Match\Details\Group\MatchGroup;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_values;
use function is_int;
use function is_string;

class Match implements MatchInterface
{
    protected const WHOLE_MATCH = 0;
    private const VALUE_INDEX = 0;

    /** @var string */
    protected $subject;
    /** @var int */
    protected $index;
    /** @var array */
    protected $matches;

    /** @var GroupNameIndexAssign */
    private $groupAssign;
    /** @var GroupFactoryStrategy */
    private $strategy;

    public function __construct(string $subject, int $index, array $matches, GroupFactoryStrategy $strategy = null)
    {
        $this->subject = $subject;
        $this->index = $index;
        $this->matches = $matches;
        $this->groupAssign = new GroupNameIndexAssign($matches);
        $this->strategy = $strategy ?? new MatchGroupFactoryStrategy();
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function index(): int
    {
        return $this->index;
    }

    public function match(): string
    {
        list($match, $offset) = $this->matches[self::WHOLE_MATCH][$this->index];
        return $match;
    }

    /**
     * @param string|int $nameOrIndex
     * @return MatchGroup
     * @throws NonexistentGroupException
     */
    public function group($nameOrIndex): MatchGroup
    {
        if (!$this->hasGroup($nameOrIndex)) {
            throw new NonexistentGroupException($nameOrIndex);
        }
        return $this->getGroupFacade($nameOrIndex)->createGroup($this->strategy);
    }

    private function getGroupFacade($nameOrIndex): GroupFacade
    {
        return new GroupFacade($this->matches, $this->subject, $nameOrIndex, $this->index);
    }

    /**
     * @return string[]
     */
    public function namedGroups(): array
    {
        $namedGroups = [];
        foreach ($this->matches as $nameOrIndex => $match) {
            if (is_string($nameOrIndex)) {
                list($value, $offset) = $match[$this->index];
                $namedGroups[$nameOrIndex] = $value;
            }
        }
        return $namedGroups;
    }

    /**
     * @return string[]
     */
    public function groupNames(): array
    {
        return array_values(array_filter(array_keys($this->matches), function ($key) {
            return is_string($key);
        }));
    }

    /**
     * @return string[]
     */
    public function groups(): array
    {
        $indexMatches = array_filter($this->matches, function (array $match, $groupIndexOrName) {
            return is_int($groupIndexOrName);
        }, ARRAY_FILTER_USE_BOTH);
        $indexGroups = array_map(function (array $match) {
            list($value, $offset) = $match[$this->index];
            return $value;
        }, $indexMatches);
        return array_slice($indexGroups, 1);
    }

    /**
     * @param string|int $nameOrIndex
     * @return bool
     */
    public function hasGroup($nameOrIndex): bool
    {
        $this->validateGroupName($nameOrIndex);
        return array_key_exists($nameOrIndex, $this->matches);
    }

    /**
     * @param string|int $nameOrIndex
     * @return bool
     * @throws NonexistentGroupException
     */
    public function matched($nameOrIndex): bool
    {
        return $this->group($nameOrIndex)->matches();
    }

    public function all(): array
    {
        return $this->getFirstFromAllMatches();
    }

    protected function getFirstFromAllMatches(): array
    {
        return array_map(function ($match) {
            list($value, $offset) = $match;
            return $value;
        }, $this->matches[self::WHOLE_MATCH]);
    }

    public function offset(): int
    {
        return ByteOffset::toCharacterOffset($this->subject, $this->byteOffset());
    }

    public function byteOffset(): int
    {
        list($value, $offset) = $this->matches[self::WHOLE_MATCH][$this->index];
        return $offset;
    }

    public function __toString(): string
    {
        return $this->match();
    }

    private function validateGroupName($nameOrIndex): void
    {
        (new GroupNameValidator($nameOrIndex))->validate();
    }
}
