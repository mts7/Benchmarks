<?php

declare(strict_types=1);

namespace Benchmark\definitions;

class EmptyArray
{
    public const ITERATIONS = 9000000;
    public static array $filled = [];

    public function __construct()
    {
        for ($i = 0; $i < 1575; $i++) {
            self::$filled[] = md5(time() . __DIR__ . 'banana');
        }
    }

    public static function benchmarkCountEmpty(): void
    {
        $array = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (count($array) === 0) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkEmptyEmpty(): void
    {
        $array = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (empty($array)) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkConstructEmpty(): void
    {
        $array = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if ($array === []) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkBooleanEmpty(): void
    {
        $array = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (!(bool)$array) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkCountFilled(): void
    {
        $array = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (count($array) === 0) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkEmptyFilled(): void
    {
        $array = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (empty($array)) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkConstructFilled(): void
    {
        $array = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if ($array === []) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkBooleanFilled(): void
    {
        $array = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (!(bool)$array) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }
}