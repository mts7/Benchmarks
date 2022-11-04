<?php

/**
 * Check for the timing for various ways for checking if a number is even.
 */

declare(strict_types=1);

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Factory\ContainerFactory;
use MtsBenchmarks\Report\ConsoleReport;

/**
 * @psalm-suppress UnusedPsalmSuppress
 * @psalm-suppress MissingFile
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * @psalm-suppress MixedArgument
 * @psalm-suppress UnusedParam
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function countEmpty(int $value): bool
{
    global $emptyArray;

    return count($emptyArray) === 0;
}

/**
 * @psalm-suppress UnusedParam
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function emptyEmpty(int $value): bool
{
    global $emptyArray;

    return empty($emptyArray);
}

/**
 * @psalm-suppress UnusedParam
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function equalsEmpty(int $value): bool
{
    global $emptyArray;

    return $emptyArray === [];
}

/**
 * @psalm-suppress UnusedParam
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function booleanEmpty(int $value): bool
{
    global $emptyArray;

    /** @noinspection PhpUnnecessaryBoolCastInspection */
    return !(bool) $emptyArray;
}

$emptyArray = [];

// define the benchmark parameters
$methods = ['countEmpty', 'emptyEmpty', 'equalsEmpty', 'booleanEmpty'];
$samples = 40;
$iterations = 112000;
$title = 'Empty Array';

$container = ContainerFactory::create();
/** @noinspection BadExceptionsProcessingInspection */
try {
    /** @var ConsoleReport $report */
    $report = $container->get(ConsoleReport::class);
    /** @var Benchmark $benchmark */
    $benchmark = $container->get(Benchmark::class, [$samples, $iterations]);
    $results = $benchmark->run($methods);
    echo $report->generate($samples, $iterations, $title, $results) . PHP_EOL;
} catch (ReflectionException) {
    echo 'The container was not properly initialized.';
} catch (Throwable $exception) {
    echo $exception->getMessage();
}
