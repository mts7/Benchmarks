<?php

declare(strict_types=1);

namespace Benchmark\definitions;

class ArrayMerge
{
    public const ITERATIONS = 40000;

    public static function benchmarkArrayMerge(): void
    {
        $output = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if ($i % 2 === 1) {
                $array = ['odd'];
                $output = array_merge($output, $array);
            } else {
                $output[] = 'even';
            }
        }
    }

    public static function benchmarkSpreadOperator(): void
    {
        $output = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if ($i % 2 === 1) {
                $array = ['odd'];
                $output = [...$output, ...$array];
            } else {
                $output[] = 'even';
            }
        }
    }
}