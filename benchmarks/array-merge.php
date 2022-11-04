<?php

/**
 * Check for the timing for various ways for checking if a number is even.
 */

declare(strict_types=1);

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Factory\ContainerFactory;

/**
 * @psalm-suppress UnusedPsalmSuppress
 * @psalm-suppress MissingFile
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * @psalm-suppress MixedArgument
 * @psalm-suppress UnusedParam
 * @noinspection PhpUnusedParameterInspection
 * @noinspection PhpUnused
 */
function arrayMerge(int $value): array
{
    global $filled, $extras;

    return array_merge($filled, $extras);
}

/**
 * @psalm-suppress UnusedParam
 * @noinspection PhpUnusedParameterInspection
 * @noinspection PhpUnused
 */
function spreadOperator(int $value): array
{
    global $filled, $extras;

    return [...$filled, ...$extras];
}

// define the benchmark parameters
$methods = ['arrayMerge', 'spreadOperator'];
$samples = 40;
$iterations = 80000;
$title = 'Array Merge';

/** @var array<int,string> $filled */
$filled = array_fill(0, 30, md5(__FILE__ . time() . microtime()));
/** @var array<int,string> $extras */
$extras = array_fill(0, 20, 'extras');

$container = ContainerFactory::create();
try {
    /** @var Benchmark $benchmark */
    $benchmark = $container->get(Benchmark::class, [$samples, $iterations, $title]);
    echo $benchmark->run($methods);
} catch (ReflectionException) {
    echo 'The container was not properly initialized.';
} catch (Throwable $exception) {
    echo $exception->getMessage();
}
