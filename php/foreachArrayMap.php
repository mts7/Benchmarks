<?php

ini_set('memory_limit', -1);

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

function timeForeach($array = []) {
  $seconds = [];
  foreach ($array as $value) {
    $seconds[] = $value['second'];
  }
  return $seconds;
}

function timeArrayMap($array = []) {
  return array_map(function ($value) {
    return $value['second'];
  }, $array);
}

function returnSecond($value) {
  return $value['second'];
}

function timeArrayMapNamed($array = []) {
  return array_map('returnSecond', $array);
}

function timeForILength($array = []) {
  $seconds = [];
  $array_length = count($array);
  for ($i = 0; $i < $array_length; $i++) {
    $seconds[] = $array[$i]['second'];
  }
  return $seconds;
}

function timeForI($array = []) {
  $seconds = [];
  for ($i = 0; $i < count($array); $i++) {
    $seconds[] = $array[$i]['second'];
  }
  return $seconds;
}

$functions = [
  'timeForeach',
  'timeArrayMap',
  'timeArrayMapNamed',
  'timeForILength',
  'timeForI',
];

$reporting = [];

for ($i = 0; $i < 20; $i++) {
  $array_three_dimension_keys = generateArrayThreeDimension();
  foreach ($functions as $func) {
    $result = timer($func, $array_three_dimension_keys);
    $reporting['3d'][$func][] = $result;
  }

  $numbers = range(0, 1000000);
  foreach ($functions as $func) {
    $result = timer($func, $numbers);
    $reporting['1d'][$func][] = $result;
  }
}

foreach ($reporting as $type => $functions) {
  echo $type . '<br />' . PHP_EOL;
  foreach ($functions as $func => $results) {
    echo $func . '<br />' . PHP_EOL;
    foreach ($results as $result) {
      echo $result . '<br />' . PHP_EOL;
    }
  }
}
