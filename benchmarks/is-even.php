<?php

/**
 * Check for the timing for various ways for checking if a number is even.
 */

declare(strict_types=1);

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Helper\ContainerFactory;

/**
 * @psalm-suppress UnusedPsalmSuppress
 * @psalm-suppress MissingFile
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Performs modular division to determine if the remainder is 0.
 *
 * Since every even number is divisible by 2 with a remainder of 0, only even
 * numbers will yield a `true` result.
 */
function modular(int $value): bool
{
    return $value % 2 === 0;
}

/**
 * Performs a bit comparison and determines if the bit is not set.
 *
 * When the 1-bit is set, the value is odd (since a binary representation of an
 * odd number will end with the 1-bit set). For a number to be even, the 1-bit
 * must not be set.
 */
function bitwise(int $value): bool
{
    return ($value & 1) === 0;
}

// define the benchmark parameters
$methods = ['modular', 'bitwise'];
$samples = 34;
$iterations = 500000;
$title = 'Is Even';

$container = ContainerFactory::create();
try {
    /** @var Benchmark $benchmark */
    $benchmark = $container->get(Benchmark::class, [$samples, $iterations, $title]);
    echo $benchmark->run($methods);
} catch (\ReflectionException) {
    echo 'The container was not properly initialized.';
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}
