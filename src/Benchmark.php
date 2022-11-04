<?php

declare(strict_types=1);

namespace MtsBenchmarks;

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

    /**
     * @param int $samples The number of durations to accumulate
     * @param int $iterations The number of times to execute the method
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly int $samples,
        private readonly int $iterations,
        private readonly TimerInterface $timer,
    ) {
    }

    /**
     * Executes the iterator for the method for the number of samples provided.
     *
     * @return array<int,float>
     *
     * @throws \MtsTimer\Exception\IncompleteTimingException
     */
    public function buildSamples(callable $method): array
    {
        $durations = [];
        for ($sample = 0; $sample < $this->samples; $sample++) {
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
     * @throws \MtsTimer\Exception\IncompleteTimingException
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
     * @throws \MtsTimer\Exception\IncompleteTimingException
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
     * @throws \MtsTimer\Exception\IncompleteTimingException
     */
    private function iterate(callable $method): float
    {
        $this->timer->reset();
        for ($i = 0; $i < $this->iterations; $i++) {
            $this->timer->start();
            $method($i);
            $this->timer->stop();
            $this->timer->addDuration();
        }

        return $this->timer->getTotalDuration();
    }
}
