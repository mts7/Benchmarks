<?php

declare(strict_types=1);

namespace MtsBenchmarks;

use MtsBenchmarks\Helper\IncrementIntegerIterator;
use MtsTimer\TimerInterface;

/**
 * Benchmark framework for executing callables and timing those executions
 */
class Benchmark
{
    /**
     * @var array<string,array<int,float>> $results
     */
    private array $results = [];

    public function __construct(
        private readonly IncrementIntegerIterator $samplesIterator,
        private readonly IncrementIntegerIterator $iterationsIterator,
        private readonly TimerInterface $timer,
    ) {
    }

    /**
     * Executes the iterator for the method for the number of samples provided.
     *
     * @return array<int,float>
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \MtsTimer\Exception\IncompleteTimingException
     * @throws \ReflectionException
     *
     * @psalm-suppress UnusedForeachValue
     */
    public function buildSamples(callable $method): array
    {
        $durations = [];
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($this->samplesIterator as $value) {
            $durations[] = $this->iterate($method);
        }

        return $durations;
    }

    /**
     * Executes the benchmark steps and returns the results array.
     *
     * @param array<int|string,callable> $methods
     *
     * @return array<string,array<int,float>>
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \MtsTimer\Exception\IncompleteTimingException
     * @throws \ReflectionException
     */
    final public function run(array $methods): array
    {
        $this->execute($methods);

        return $this->results;
    }

    /**
     * Executes each callable method and stores the results.
     *
     * @param array<int|string,callable> $methods
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \MtsTimer\Exception\IncompleteTimingException
     * @throws \ReflectionException
     */
    private function execute(array $methods): void
    {
        foreach ($methods as $key => $callable) {
            if (is_int($key)) {
                /** @var string $key */
                $key = $callable;
            }
            $this->results[$key] = $this->buildSamples($callable);
        }
    }

    /**
     * Iterates over the callable $method and returns the loop duration.
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \MtsTimer\Exception\IncompleteTimingException
     * @throws \ReflectionException
     *
     * @noinspection DisconnectedForeachInstructionInspection
     */
    private function iterate(callable $method): float
    {
        $this->timer->reset();
        foreach ($this->iterationsIterator as $value) {
            $this->timer->start();
            $method($value);
            $this->timer->stop();
            $this->timer->addDuration();
        }

        return $this->timer->getTotalDuration();
    }
}
