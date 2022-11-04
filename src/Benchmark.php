<?php

declare(strict_types=1);

namespace MtsBenchmarks;

use MtsBenchmarks\Helper\Calculate;
use MtsBenchmarks\Helper\Formatter;
use MtsTimer\TimerInterface;

/**
 * Display helpers for benchmarking
 */
class Benchmark
{
    public const COLUMN_WIDTH = 17;
    public const SEPARATOR = ' | ';

    /**
     * @var array<string,array<int,float|string>> $results
     */
    private array $results = [];

    /**
     * @param int $samples The number of durations to accumulate
     * @param int $iterations The number of times to execute the method
     * @param string $title The title of the benchmark
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly int $samples,
        private readonly int $iterations,
        private readonly string $title,
        private readonly Calculate $calculator,
        private readonly TimerInterface $timer,
    ) {
    }

    /**
     * Builds the standard headers for the benchmark.
     */
    public function buildHeaders(): string
    {
        $output = self::SEPARATOR . $this->pad($this->title) . self::SEPARATOR;
        $headers = ['Average', 'Minimum', 'Maximum', 'Chopped Average'];
        foreach ($headers as $header) {
            $output .= $this->pad($header) . self::SEPARATOR;
        }
        $output .= PHP_EOL;

        $columns = 1 + count($headers);
        $width = $columns * self::COLUMN_WIDTH + ($columns + 1) * strlen(self::SEPARATOR);

        return $output . str_pad('', $width, '=') . PHP_EOL;
    }

    /**
     * Builds the result text based on the calculations of the results.
     */
    public function buildResults(): string
    {
        $output = '';
        foreach ($this->results as $method => $durations) {
            $elements = [$method, ...$this->calculate($durations)];
            $output .= self::SEPARATOR;
            foreach ($elements as $element) {
                $output .= $this->pad($element) . self::SEPARATOR;
            }
            $output .= PHP_EOL;
        }

        return $output;
    }

    /**
     * Executes the iterator for the method for the number of samples provided.
     *
     * @return array<int,string>
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
     * Builds the title for the benchmark.
     */
    public function buildTitle(): string
    {
        return sprintf(
            'Benchmarking %s over %s iterations with %s samples',
            $this->title,
            $this->iterations,
            $this->samples
        )
            . PHP_EOL . PHP_EOL;
    }

    /**
     * Executes each callable method and stores the results.
     *
     * @param array<int|string,callable> $methods
     *
     * @throws \MtsTimer\Exception\IncompleteTimingException
     */
    public function execute(array $methods): void
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
     * Executes the benchmark steps and returns the result message.
     *
     * @param array<int|string,callable> $methods
     *
     * @throws \MtsTimer\Exception\IncompleteTimingException
     */
    final public function run(array $methods): string
    {
        $output = $this->buildTitle();
        $output .= $this->buildHeaders();
        $this->execute($methods);

        return $output . $this->buildResults();
    }

    /**
     * Calculates the average, minimum, and maximum durations.
     *
     * @param array<float|string> $values
     *
     * @return array<int,string>
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function calculate(array $values): array
    {
        $average = $this->calculator->average($values);
        /** @var float $minimum */
        $minimum = min($values);
        /** @var float $maximum */
        $maximum = max($values);
        $condensed = $this->calculator->choppedAverage($values);

        return array_map(static function (float|string $float): string {
            return Formatter::toDecimal($float);
        }, [$average, $minimum, $maximum, $condensed]);
    }

    /**
     * Iterates over the callable $method and returns the loop duration.
     * @throws \MtsTimer\Exception\IncompleteTimingException
     */
    private function iterate(callable $method): string
    {
        $this->timer->reset();
        for ($i = 0; $i < $this->iterations; $i++) {
            $this->timer->start();
            $method($i);
            $this->timer->stop();
            $this->timer->addDuration();
        }

        return Formatter::toDecimal($this->timer->getTotalDuration());
    }

    private function pad(string $string = ''): string
    {
        return str_pad($this->truncate($string), self::COLUMN_WIDTH);
    }

    private function truncate(string $input): string
    {
        $output = $input;
        if (strlen($input) > self::COLUMN_WIDTH) {
            $output = substr($input, 0, self::COLUMN_WIDTH - 3) . '...';
        }

        return $output;
    }
}
