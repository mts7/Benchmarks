# Benchmarks

A framework for benchmarking simple functionality

## Installation

```shell
composer require --dev mts7/Benchmarks --prefer-dist
```

## Usage

Benchmarks includes a
[container](https://github.com/mts7/php-dependency-injection) for dependency
injection, which makes setting up the benchmark quicker and easier.

```php
$container = \MtsBenchmarks\Factory\ContainerFactory::create();
```

### Single Method Call

The `run` method accepts an array of callables and will execute each callable
for the number of iterations per sample for the number of samples and provides
the callable with a parameter of the current iteration. All the necessary steps
to benchmark are included in the correct order by using the `run` method.

After calling `run`, any report class can accept the results and process them.

```php
$samples = 5;
$iterations = 100;
$title = 'callable name';

$benchmark = $container->get(\MtsBenchmarks\Benchmark::class, [$samples, $iterations]);

$results = $benchmark->run(['callable']);

$report = $container->get(\MtsBenchmarks\Report\ConsoleReport::class);

echo $report->generate($samples, $iterations, $title, $results);
```

### Multiple Method Calls

In this form, the framework provides the time calculation results of each
callable rather than the raw data. Output formatting is handled by a report
object.

```php
$samples = 5;
$iterations = 100;
$title = 'callable name';

$benchmark = $container->get(\MtsBenchmarks\Benchmark::class, [$samples, $iterations]);
$report = $container->get(\MtsBenchmarks\Report\ConsoleReport::class);

$output = $report->buildTitle($samples, $iterations, $title);
$output .= $report->buildHeaders($title);
$results = $benchmark->run(['callable']);
$output .= $report->buildResults($results);

echo $output;
```

When there is only one callable to check or a desire or preference to test each
callable independently (without the framework), `buildSamples` will work. This
is the internal method that calls the callable and sets up timing.

```php
// raw durations from executing the callable $iterations times per sample
$durations = $benchmark->buildSamples('callable');
```

## Comparisons

The framework does show the users which subjects under test perform quicker than
others and by how much, meaning this is a useful tool for comparison rather than
for timing.

While the framework handles the timing and execution of the subjects under test,
it does not handle the creation or organization of the subjects under test.
Since each system is different, there are no constant times for any of the
benchmarks across systems.

On a MacBook Pro with M1 Pro, some benchmarks were 2.5 times longer than running
with a smaller version of this framework. The base benchmark times on the same
system took 2 times longer to execute than on
[PHP Sandbox](https://onlinephp.io).

## Future Enhancements

- increase speed/performance
- add additional report options (like HTML, XML)
- add timing and memory usage in ConsoleReport output
- allow benchmark functions to have any number of parameters rather than requiring an int

## Included Benchmarks

Note these are not included in the distribution package and are only available
in the source.

```shell
composer require --dev mts7/Benchmarks --prefer-source
```

- [Array Iterator](benchmarks/array-iterator.php)
- [Array Merge](benchmarks/array-merge.php)
- [Array Unique](benchmarks/array-unique.php)
  -  This uses the `DS` PECL extension to test `Set`
- [Empty Array](benchmarks/empty-array.php)
- [Is Even](benchmarks/is-even.php)

All of these benchmarks are ready to execute by running
`php benchmarks/{file-name}.php`. 
