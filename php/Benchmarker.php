<?php

declare(strict_types=1);

namespace Benchmark;

class Benchmarker
{
    private array $durations;
    private float $end;
    private string $name = '';
    private float $start;
    private int $testCount;

    public function __construct()
    {
        $this->reset();
    }

    private function displayDurations(): void
    {
        foreach ($this->durations as $duration) {
            echo $duration . PHP_EOL;
        }
    }

    public function getAverage(): float
    {
        return array_sum($this->durations) / count($this->durations);
    }

    private function getDuration(): float
    {
        return $this->end - $this->start;
    }

    public function report(): void
    {
        echo "{$this->name} benchmark" . PHP_EOL;

        echo 'Average time: ' . $this->getAverage() . PHP_EOL;

        $this->displayDurations();
    }

    private function reset(): void
    {
        $this->durations = [];
        $this->end = 0.0;
        $this->start = 0.0;
        $this->testCount = 5;
    }

    public function run(callable $test, $arguments): void
    {
        $this->start = microtime(true);
        $test($arguments);
        $this->end = microtime(true);
    }

    public function runMultiple(callable $test, $arguments): void
    {
        $this->reset();
        for ($i = 0; $i < $this->testCount; $i++) {
            $this->run($test, $arguments);
            $this->durations[] = $this->getDuration();
        }
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}