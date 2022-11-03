<?php

declare(strict_types=1);

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Factory\ContainerFactory;

ini_set('memory_limit', -1);
ini_set('max_execution_time', 180);

/**
 * @psalm-suppress UnusedPsalmSuppress
 * @psalm-suppress MissingFile
 */
require dirname(__DIR__) . '/vendor/autoload.php';

function foreachString(): array
{
    global $arrayString;
    $tester = [];
    /** @var string $value */
    foreach ($arrayString as $value) {
        $tester[] = $value;
    }

    return $tester;
}

/**
 * @psalm-suppress MixedArgument
 */
function arrayMapString(): array
{
    global $arrayString;

    return array_map(static function (string $value): string {
        return $value;
    }, $arrayString);
}

function returnSecond(string $value): string
{
    return $value;
}

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedArgument
 */
function arrayMapNamedString(): array
{
    global $arrayString;

    return array_map('returnSecond', $arrayString);
}

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedArgument
 */
function forILengthString(): array
{
    global $arrayString;
    $seconds = [];
    $arrayLength = count($arrayString);
    for ($i = 0; $i < $arrayLength; $i++) {
        /** @var string $value */
        $value = $arrayString[$i];
        $seconds[] = $value;
    }

    return $seconds;
}

/**
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedArgument
 */
function forICountString(): array
{
    global $arrayString;
    $seconds = [];
    for ($i = 0; $i < count($arrayString); $i++) {
        /** @var string $value */
        $value = $arrayString[$i];
        $seconds[] = $value;
    }

    return $seconds;
}

/**
 * @throws \Exception
 */
function foreachNumber(): array
{
    global $arrayNumber;
    $products = [];
    /** @var int $value */
    foreach ($arrayNumber as $value) {
        $products[] = $value * random_int(2, 9);
    }

    return $products;
}

/**
 * @throws \Exception
 *
 * @psalm-suppress MixedArgument
 */
function arrayMapNumber(): array
{
    global $arrayNumber;

    return array_map(
    /**
     * @throws \Exception
     */
        static function (int $value): int {
            return $value * random_int(2, 9);
        },
        $arrayNumber
    );
}

/**
 * @throws \Exception
 */
function returnProduct(float|int $value = 0): float|int
{
    return $value * random_int(2, 9);
}

/**
 * @throws \Exception
 *
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedArgument
 */
function arrayMapNamedNumber(): array
{
    global $arrayNumber;

    return array_map('returnProduct', $arrayNumber);
}

/**
 * @throws \Exception
 *
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedArgument
 */
function forILengthNumber(): array
{
    global $arrayNumber;
    $products = [];
    $arrayLength = count($arrayNumber);
    for ($i = 0; $i < $arrayLength; $i++) {
        /** @var int $value */
        $value = $arrayNumber[$i];
        $products[] = $value * random_int(2, 9);
    }

    return $products;
}

/**
 * @throws \Exception
 *
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedArgument
 */
function forICountNumber(): array
{
    global $arrayNumber;
    $seconds = [];
    for ($i = 0; $i < count($arrayNumber); $i++) {
        /** @var int $value */
        $value = $arrayNumber[$i];
        $seconds[] = $value * random_int(2, 9);
    }

    return $seconds;
}

$filler = 546573;
// The below arrays are used as globals for the functions.
// phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable
$arrayString = array_fill(0, $filler, md5(random_bytes(8)));
// phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable
$arrayNumber = array_fill(0, $filler, random_int(2, 9999));

$samples = 30;
$iterations = 3;

$container = ContainerFactory::create();
try {
    /** @var Benchmark $benchmarkString */
    $benchmarkString = $container->get(Benchmark::class, [$samples, $iterations, 'String[] Iterator']);
    echo $benchmarkString->run([
        'foreachString',
        'arrayMapString',
        'arrayMapNamedString',
        'forILengthString',
        'forICountString',
    ]);
    echo PHP_EOL;

    /** @var Benchmark $benchmarkNumber */
    $benchmarkNumber = $container->get(Benchmark::class, [$samples, $iterations, 'Number[] Iterator']);
    echo $benchmarkNumber->run([
        'foreachNumber',
        'arrayMapNumber',
        'arrayMapNamedNumber',
        'forILengthNumber',
        'forICountNumber',
    ]);
} catch (\ReflectionException) {
    echo 'The container was not properly initialized.';
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}
