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

    public static function benchmarkCount(array $array): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (count($array) === 0) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkEmpty(array $array): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (empty($array)) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkConstruct(array $array): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if ($array === []) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }

    public static function benchmarkBoolean(array $array): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            if (!(bool)$array) {
                $x = 1;
            } else {
                $x = 2;
            }
        }
    }
}