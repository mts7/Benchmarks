<?php

declare(strict_types=1);

namespace MtsBenchmarks\Helpers;

/**
 * Perform calculations related to benchmarks.
 */
final class Calculate
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
}
