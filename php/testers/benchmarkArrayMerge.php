<?php

include_once dirname(__DIR__) . '/bootstrap.php';

use Benchmark\Benchmarker;
use Benchmark\definitions\ArrayMerge;

$tests = [
    [
        'name' => 'Array Merge - array merge over ' . ArrayMerge::ITERATIONS . ' iterations',
        'callable' => [ArrayMerge::class, 'benchmarkArrayMerge'],
        'arguments' => null,
    ],
    [
        'name' => 'Array Merge - spread operator over ' . ArrayMerge::ITERATIONS . ' iterations',
        'callable' => [ArrayMerge::class, 'benchmarkSpreadOperator'],
        'arguments' => null,
    ],
];

$benchmarker = new Benchmarker();

foreach ($tests as $test) {
    $benchmarker->setName($test['name']);
    $benchmarker->runMultiple($test['callable'], $test['arguments']);
    $benchmarker->report();

    echo 'Average per iteration: ' . $benchmarker->getAverage() / ArrayMerge::ITERATIONS . PHP_EOL;
}
