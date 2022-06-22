<?php

include_once dirname(__DIR__) . '/bootstrap.php';

use Benchmark\Benchmark;
use Benchmark\definitions\ArrayMerge;

$benchmarker = new Benchmark(['class' => ArrayMerge::class]);
$benchmarker->go();
