<?php

declare(strict_types=1);

namespace MtsBenchmarks\Report;

use MtsBenchmarks\Helper\Calculate;

/**
 * Report for the console
 */
class ConsoleReport implements ReportInterface
{
    public const COLUMN_WIDTH = 17;
    public const SEPARATOR = ' | ';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly Calculate $calculator)
    {
    }

    /**
     * Builds the standard headers for the benchmark.
     */
    public function buildHeaders(string $title): string
    {
        $output = self::SEPARATOR . $this->pad($title) . self::SEPARATOR;
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
     *
     * @param array<string,array<int,float>> $results
     */
    public function buildResults(array $results): string
    {
        $output = '';
        foreach ($results as $method => $durations) {
            $elements = [$method, ...$this->calculator->summary($durations)];
            $output .= self::SEPARATOR;
            foreach ($elements as $element) {
                $output .= $this->pad($element) . self::SEPARATOR;
            }
            $output .= PHP_EOL;
        }

        return $output;
    }

    /**
     * Builds the title for the benchmark.
     */
    public function buildTitle(int $samples, int $iterations, string $title): string
    {
        return sprintf(
            'Benchmarking %s over %s iterations with %s samples',
            $title,
            $iterations,
            $samples
        )
            . PHP_EOL . PHP_EOL;
    }

    /**
     * Calls all the display methods in order and returns their output.
     *
     * @param array<string,array<int,float>> $results
     */
    public function generate(int $samples, int $iterations, string $title, array $results): string
    {
        $output = $this->buildTitle($samples, $iterations, $title);
        $output .= $this->buildHeaders($title);
        $output .= $this->buildResults($results);

        return $output;
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
