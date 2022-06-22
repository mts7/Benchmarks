<?php

include_once dirname(__DIR__) . '/bootstrap.php';

use Benchmark\Benchmarker;
use Benchmark\definitions\EmptyArray;

$tests = [
    [
        'name' => 'Empty Array - count (empty array) over ' . EmptyArray::ITERATIONS . ' iterations',
        'callable' => [EmptyArray::class, 'benchmarkCount'],
        'arguments' => [],
    ],
    [
        'name' => 'Empty Array - empty (empty array) over ' . EmptyArray::ITERATIONS . ' iterations',
        'callable' => [EmptyArray::class, 'benchmarkEmpty'],
        'arguments' => [],
    ],
    [
        'name' => 'Empty Array - construct (empty array) over ' . EmptyArray::ITERATIONS . ' iterations',
        'callable' => [EmptyArray::class, 'benchmarkConstruct'],
        'arguments' => [],
    ],
    [
        'name' => 'Empty Array - boolean (empty array) over ' . EmptyArray::ITERATIONS . ' iterations',
        'callable' => [EmptyArray::class, 'benchmarkBoolean'],
        'arguments' => [],
    ],
    [
        'name' => 'Empty Array - count (filled array) over ' . EmptyArray::ITERATIONS . ' iterations',
        'callable' => [EmptyArray::class, 'benchmarkCount'],
        'arguments' => EmptyArray::$filled,
    ],
    [
        'name' => 'Empty Array - empty (filled array) over ' . EmptyArray::ITERATIONS . ' iterations',
        'callable' => [EmptyArray::class, 'benchmarkEmpty'],
        'arguments' => EmptyArray::$filled,
    ],
    [
        'name' => 'Empty Array - construct (filled array) over ' . EmptyArray::ITERATIONS . ' iterations',
        'callable' => [EmptyArray::class, 'benchmarkConstruct'],
        'arguments' => EmptyArray::$filled,
    ],
    [
        'name' => 'Empty Array - boolean (filled array) over ' . EmptyArray::ITERATIONS . ' iterations',
        'callable' => [EmptyArray::class, 'benchmarkBoolean'],
        'arguments' => EmptyArray::$filled,
    ],
];

$benchmarker = new Benchmarker();

foreach ($tests as $test) {
    $benchmarker->setName($test['name']);
    $benchmarker->runMultiple($test['callable'], $test['arguments']);
    $benchmarker->report();
}
