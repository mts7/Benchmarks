<?php

include_once dirname(__DIR__) . '/bootstrap.php';

use Benchmark\Benchmark;
use Benchmark\definitions\EmptyArray;

$benchmarker = new Benchmark(['class' => EmptyArray::class]);
$benchmarker->go();
