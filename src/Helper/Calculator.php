<?php

declare(strict_types=1);

namespace MtsBenchmarks\Helper;

/**
 * Perform calculations related to benchmarks.
 */
final class Calculator
{
    public function average(array $values): float
    {
        return array_sum($values) / count($values);
    }

    /**
     * Calculates the average of the array provided without the end values.
     */
    public function choppedAverage(array $values): float
    {
        if (count($values) < 3) {
            return $this->average($values);
        }

        $copy = $values;
        sort($copy);
        array_shift($copy);
        array_pop($copy);

        return $this->average($copy);
    }

    /**
     * Calculates the average, minimum, maximum and condensed average.
     *
     * @param array<float|string> $values
     *
     * @return array<int,string>
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function summary(array $values): array
    {
        $average = $this->average($values);
        /** @var float $minimum */
        $minimum = min($values);
        /** @var float $maximum */
        $maximum = max($values);
        $condensed = $this->choppedAverage($values);

        return array_map(static function (float|string $float): string {
            return Formatter::toDecimal($float);
        }, [$average, $minimum, $maximum, $condensed]);
    }
}
