<?php

declare(strict_types=1);

namespace Benchmark;

/**
 * Benchmark test runner
 * @package Benchmark
 * @author Mike Rodarte
 */
class Benchmark
{
    // benchmark suite properties
    /**
     * @var array Methods in the class with a format like benchmarkTestThisOne
     */
    private array $benchmarkMethods = [];

    /**
     * @var string Fully-qualified class name that contains benchmark tests
     */
    private string $class = '';

    // per benchmark test properties
    /**
     * @var string Name of the current benchmark test
     */
    private string $name = '';

    /**
     * @var array Durations of all the benchmark tests executed for this method
     */
    private array $durations = [];

    /**
     * @var float Benchmark end time as a float
     */
    private float $end = 0.0;

    /**
     * @var float Benchmark start time as a float
     */
    private float $start = 0.0;

    /**
     * @var int Number of iterations to execute each benchmark test
     */
    private int $testCount = 5;

    /**
     * @param array $config Should contain 'class' key with class name.
     */
    public function __construct(array $config = [])
    {
        $this->resetBenchmark();
        $this->setValues($config);
    }

    /**
     * Displays each duration for this benchmark test.
     */
    private function displayDurations(): void
    {
        foreach ($this->durations as $duration) {
            echo $duration . PHP_EOL;
        }
    }

    /**
     * Finds and sets the benchmark methods for the provided class.
     */
    private function findBenchmarks(): void
    {
        $this->benchmarkMethods = array_filter(get_class_methods($this->class), static function ($method) {
            return strpos($method, 'benchmark') === 0;
        });
    }

    /**
     * Splits the words, removes the first word, then adds a space between words.
     *
     * The first word is removed since this is typically used for cleansing a
     * method name, and method names start with `benchmark`.
     */
    private function formatName(string $name): string
    {
        $words = $this->splitWords($name);
        // remove the first word
        array_shift($words);
        return implode(' ', $words) . PHP_EOL;
    }

    /**
     * Calculates and returns the average of the durations.
     */
    private function getAverage(): float
    {
        return array_sum($this->durations) / count($this->durations);
    }

    /**
     * Calculates the duration of the benchmark.
     */
    private function getDuration(): float
    {
        return $this->end - $this->start;
    }

    /**
     * Executes the benchmark.
     *
     * This is the entrypoint of the app and handles finding the benchmark
     * methods from the class, running each benchmark, and displaying the
     * report.
     */
    final public function go(): void
    {
        $this->findBenchmarks();
        foreach ($this->benchmarkMethods as $method) {
            $this->setName($method);
            $this->runMultiple([$this->class, $method]);
            $this->report();
        }
    }

    /**
     * Displays the name, average times, and actual times of the benchmark.
     */
    private function report(): void
    {
        echo $this->formatName($this->name);
        echo 'Average time: ' . $this->getAverage() . PHP_EOL;
        echo 'Average per iteration: ' . $this->getAverage() / $this->class::ITERATIONS . PHP_EOL;

        $this->displayDurations();

        echo PHP_EOL;
    }

    /**
     * Resets the benchmark settings, preparing for the next benchmark.
     */
    private function resetBenchmark(): void
    {
        $this->durations = [];
        $this->end = 0.0;
        $this->start = 0.0;
        $this->testCount = 5;
    }

    /**
     * Executes a benchmark test with time wrappers.
     * @param callable $test The actual benchmark to run
     */
    private function run(callable $test): void
    {
        $this->start = microtime(true);
        $test();
        $this->end = microtime(true);
    }

    /**
     * Resets the benchmark and executes the provided benchmark test.
     * @param callable $test The actual benchmark to run
     */
    private function runMultiple(callable $test): void
    {
        $this->resetBenchmark();
        for ($i = 0; $i < $this->testCount; $i++) {
            $this->run($test);
            $this->durations[] = $this->getDuration();
        }
    }

    /**
     * Sets the class name with the benchmark tests.
     * @param string $class Fully-qualified class name
     * @property string $class
     */
    private function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * Sets the test name (defaults to the method name).
     * @property string $name
     */
    private function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Sets the test count value.
     * @property int $testCount
     */
    private function setTestCount(int $iterations): void
    {
        $this->testCount = $iterations;
    }

    /**
     * Sets the values based on this object's setters.
     * @param array $values Key-value pairs of valid values
     */
    private function setValues(array $values): void
    {
        foreach ($values as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Splits words by capital letters, returning an array of words.
     */
    private function splitWords(string $word): array
    {
        return preg_split('/(?=[A-Z])/', $word);
    }
}