<?php

ini_set('memory_limit', -1);
ini_set('max_execution_time', 180);

function generateArrayThreeDimension() {
  $array = [];
  for ($i = 0; $i < 500000; $i++) {
    $array[] = [
      'first' => [
        mt_rand(1, 100),
        mt_rand(1, 100),
        mt_rand(1, 100),
      ],
      'second' => substr(md5(time()), 8, 10),
    ];
  }
  return $array;
}

function timer($func = '', $array = []) {
  $start = microtime(true);
  $func($array);
  $end = microtime(true);

  return $end - $start;
}

function timeForeachString($array = []) {
  $seconds = [];
  foreach ($array as $value) {
    $seconds[] = $value['second'];
  }
  return $seconds;
}

function timeArrayMapString($array = []) {
  return array_map(function ($value) {
    return $value['second'];
  }, $array);
}

function returnSecond($value) {
  return $value['second'];
}

function timeArrayMapNamedString($array = []) {
  return array_map('returnSecond', $array);
}

function timeForILengthString($array = []) {
  $seconds = [];
  $array_length = count($array);
  for ($i = 0; $i < $array_length; $i++) {
    $seconds[] = $array[$i]['second'];
  }
  return $seconds;
}

function timeForIString($array = []) {
  $seconds = [];
  for ($i = 0; $i < count($array); $i++) {
    $seconds[] = $array[$i]['second'];
  }
  return $seconds;
}

function timeForeachNumber($array = []) {
  $products = [];
  foreach ($array as $value) {
    $products[] = $value * mt_rand(2, 9);
  }
  return $products;
}

function timeArrayMapNumber($array = []) {
  return array_map(function ($value) {
    return $value * mt_rand(2, 9);
  }, $array);
}

function returnProduct($value = 0) {
  return $value * mt_rand(2, 9);
}

function timeArrayMapNamedNumber($array = []) {
  return array_map('returnProduct', $array);
}

function timeForILengthNumber($array = []) {
  $products = [];
  $array_length = count($array);
  for ($i = 0; $i < $array_length; $i++) {
    $products[] = $array[$i] * mt_rand(2, 9);
  }
  return $products;
}

function timeForINumber($array = []) {
  $seconds = [];
  for ($i = 0; $i < count($array); $i++) {
    $seconds[] = $array[$i] * mt_rand(2, 9);
  }
  return $seconds;
}

$functions_multi_string = [
  'timeForeachString',
  'timeArrayMapString',
  'timeArrayMapNamedString',
  'timeForILengthString',
  'timeForIString',
];

$functions_single_number = [
  'timeForeachNumber',
  'timeArrayMapNumber',
  'timeArrayMapNamedNumber',
  'timeForILengthNumber',
  'timeForINumber',
];

$reporting = [];

// create arrays for benchmarking
$array_three_dimension_keys = generateArrayThreeDimension();
$numbers = range(0, 1000000);

$number_of_results = 20;

// run the benchmarks multiple times to get report values
for ($i = 0; $i < $number_of_results; $i++) {
  foreach ($functions_multi_string as $func) {
    $result = timer($func, $array_three_dimension_keys);
    $reporting['Three Dimensions with Strings'][$func][] = $result;
  }

  foreach ($functions_single_number as $func) {
    $result = timer($func, $numbers);
    $reporting['Single Dimension with Numbers'][$func][] = $result;
  }
}

// TODO: send this to a reporting class that uses templates
// display reports
echo 'Number of Iterations: ' . $number_of_results . '<br />' . PHP_EOL;
foreach ($reporting as $type => $functions) {
  echo $type . '<br />' . PHP_EOL;
  echo '<table>' . PHP_EOL;
  echo '<tr><th>Function</th><th>Average</th><th>Min</th><th>Max</th></tr>' . PHP_EOL;
  foreach ($functions as $func => $results) {
    echo '<tr>' . PHP_EOL;
    echo '<td>' . $func . '</td>' . PHP_EOL;
    // get average
    echo '<td>' . array_sum($results) / $number_of_results . '</td>' . PHP_EOL;
    // get min
    echo '<td>' . min($results) . '</td>' . PHP_EOL;
    echo '<td>' . max($results) . '</td>' . PHP_EOL;
    echo '</tr>' . PHP_EOL;
  }
  echo '</table>' . PHP_EOL;
}
