<?php
namespace TRegx\CleanRegex\Match\ForFirst;

use Throwable;
use TRegx\CleanRegex\Exception\CleanRegex\NoFirstElementFluentException;
use TRegx\CleanRegex\Internal\Factory\NotMatchedWorker;

class NotMatchedFluentOptional implements Optional
{
    /** @var NotMatchedWorker */
    private $worker;

    public function __construct(NotMatchedWorker $worker)
    {
        $this->worker = $worker;
    }

    public function orThrow(string $exceptionClassName = NoFirstElementFluentException::class): void
    {
        throw $this->worker->orThrow($exceptionClassName);
    }

    public function orReturn($substitute)
    {
        return $substitute;
    }

    public function orElse(callable $substituteProducer)
    {
        return $this->worker->orElse($substituteProducer);
    }
}