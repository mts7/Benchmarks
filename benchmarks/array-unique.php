<?php

/**
 * Find the best way to obtain unique values from an array
 */

declare(strict_types=1);

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Factory\ContainerFactory;
use MtsBenchmarks\Helper\IncrementIntegerIterator;
use MtsBenchmarks\Report\ConsoleReport;

/**
 * @psalm-suppress UnusedPsalmSuppress
 * @psalm-suppress MissingFile
 */
require dirname(__DIR__) . '/vendor/autoload.php';

$minimum = 0;
$maximum = 777;
$subject = range($minimum, $maximum);
$total = 1000;

for ($i = $maximum; $i < $total; $i++) {
    $subject[] = random_int($minimum, $maximum);
}

function unique_keys(): array
{
    global $subject;

    $items = [];
    foreach ($subject as $value) {
        $items[$value] = true;
    }

    return array_keys($items);
}

function unique_elements(): array
{
    global $subject;

    $items = [];
    foreach ($subject as $value) {
        $items[] = $value;
    }

    return array_unique($items);
}

function set_structure(): array
{
    global $subject;

    $items = new \Ds\Set();
    foreach ($subject as $value) {
        $items->add($value);
    }

    return $items->toArray();
}

// define the benchmark parameters
$methods = ['unique_keys', 'unique_elements'];
if (extension_loaded('ds')) {
    $methods[] = 'set_structure';
}
$samples = 70;
$iterations = 1000;
$title = 'Unique Values';

$container = ContainerFactory::create();
/** @noinspection BadExceptionsProcessingInspection */
try {
    /** @var ConsoleReport $report */
    $report = $container->get(ConsoleReport::class);
    /** @var Benchmark $benchmark */
    $benchmark = $container->get(Benchmark::class,
        [
            $container->get(IncrementIntegerIterator::class, [$samples]),
            $container->get(IncrementIntegerIterator::class, [$iterations]),
        ]
    );
    $results = $benchmark->run($methods);
    echo $report->generate($samples, $iterations, $title, $results) . PHP_EOL;
} catch (ReflectionException) {
    echo 'The container was not properly initialized.';
} catch (Throwable $exception) {
    echo $exception->getMessage();
}
