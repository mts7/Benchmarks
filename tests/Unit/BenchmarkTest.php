<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Unit;

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Helper\ContainerFactory;
use MtsBenchmarks\Helper\Formatter;
use MtsDependencyInjection\Container;
use MtsTimer\FixedTimer;
use MtsTimer\TimerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
final class BenchmarkTest extends TestCase
{
    private const TIMER_DURATION = 0.0125;

    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = ContainerFactory::create();
        $this->container->set(TimerInterface::class, FixedTimer::class);
    }

    /**
     * @dataProvider titleData
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testBuildHeaders(string $title, string $expectedTitle): void
    {
        $expected = " | {$expectedTitle} | Average           | Minimum           | Maximum           | Chopped Average   | ";
        $expected .= PHP_EOL . str_repeat('=', 103) . PHP_EOL;
        $fixture = $this->getBenchmark($title);

        $actual = $fixture->buildHeaders();

        self::assertSame($expected, $actual);
    }

    public function titleData(): iterable
    {
        yield 'excessively-long title' => [
            'title' => md5(__FUNCTION__),
            'expectedTitle' => substr(md5(__FUNCTION__), 0, Benchmark::COLUMN_WIDTH - 3) . '...',
        ];

        yield 'maximum-length title' => [
            'title' => substr(md5(__FUNCTION__), 0, Benchmark::COLUMN_WIDTH),
            'expectedTitle' => substr(md5(__FUNCTION__), 0, Benchmark::COLUMN_WIDTH),
        ];

        yield 'short title' => [
            'title' => 'tester',
            'expectedTitle' => 'tester           ',
        ];
    }

    /**
     * @param array<int|string,callable> $callables
     * @param array<int,array<string,string|int|float>> $expected
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     *
     * @dataProvider buildResultsData
     */
    public function testBuildResults(array $callables, array $expected): void
    {
        $this->setTimerDurations();
        $fixture = $this->getBenchmark(__FUNCTION__);

        $fixture->execute($callables);
        $actual = $fixture->buildResults();
        $results = $this->extractResults($actual);

        if (empty($expected)) {
            self::assertSame($expected, $results);

            return;
        }

        foreach ($results as $index => $result) {
            self::assertSame($expected[$index]['method'], $result['method']);
            // verify the time is greater than the minimum
            self::assertSame(Formatter::toDecimal($expected[$index]['average']), $result['average']);
            self::assertSame(Formatter::toDecimal($expected[$index]['minimum']), $result['minimum']);
            self::assertSame(Formatter::toDecimal($expected[$index]['maximum']), $result['maximum']);
            self::assertSame(Formatter::toDecimal($expected[$index]['chopped']), $result['chopped']);
            // verify all the results are the same since there is only 1 sample and 1 iteration
            self::assertSame($result['average'], $result['minimum']);
            self::assertSame($result['average'], $result['maximum']);
            self::assertSame($result['average'], $result['chopped']);
        }
    }

    /**
     * @return iterable<string,array<string,array>>
     */
    public function buildResultsData(): iterable
    {
        yield 'empty results' => [
            'methods' => [],
            'expected' => [],
        ];

        yield 'no Op once' => [
            'methods' => [
                'noOp1' => [self::class, 'noOp'],
            ],
            'expected' => [
                [
                    'method' => str_pad('noOp1', Benchmark::COLUMN_WIDTH),
                    'average' => self::TIMER_DURATION,
                    'minimum' => self::TIMER_DURATION,
                    'maximum' => self::TIMER_DURATION,
                    'chopped' => self::TIMER_DURATION,
                ],
            ]
        ];

        yield 'no Op twice' => [
            'methods' => [
                'noOp1' => [self::class, 'noOp'],
                'noOp2' => [self::class, 'noOp'],
            ],
            'expected' => [
                [
                    'method' => str_pad('noOp1', Benchmark::COLUMN_WIDTH),
                    'average' => self::TIMER_DURATION,
                    'minimum' => self::TIMER_DURATION,
                    'maximum' => self::TIMER_DURATION,
                    'chopped' => self::TIMER_DURATION,
                ],
                [
                    'method' => str_pad('noOp2', Benchmark::COLUMN_WIDTH),
                    'average' => self::TIMER_DURATION,
                    'minimum' => self::TIMER_DURATION,
                    'maximum' => self::TIMER_DURATION,
                    'chopped' => self::TIMER_DURATION,
                ],
            ]
        ];
    }

    /**
     * @dataProvider buildSamplesData
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testBuildSamples(int $samples): void
    {
        $this->setTimerDurations();
        $title = __FUNCTION__;
        $callable = [self::class, 'noOp'];
        $fixture = $this->getBenchmark($title, $samples);

        $durations = $fixture->buildSamples($callable);

        self::assertCount($samples, $durations);
        self::assertSame(Formatter::toDecimal(self::TIMER_DURATION), $durations[$samples - 1]);
    }

    public function buildSamplesData(): iterable
    {
        yield 'one' => [
            'samples' => 1,
        ];

        yield 'two' => [
            'samples' => 2,
        ];
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testBuildTitle(): void
    {
        $title = __FUNCTION__;
        $expected = "Benchmarking {$title} over 1 iterations with 1 samples" . PHP_EOL . PHP_EOL;
        $fixture = $this->getBenchmark($title);

        $actual = $fixture->buildTitle();

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider titleData
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testRun(string $title, string $expectedTitle): void
    {
        $this->setTimerDurations();
        $callables = ['noOp' => [self::class, 'noOp'], 'is_int'];
        $expectedLines = [
            "Benchmarking {$title} over 1 iterations with 1 samples",
            '',
            " | {$expectedTitle} | Average           | Minimum           | Maximum           | Chopped Average   | ",
            '=======================================================================================================',
        ];
        $fixture = $this->getBenchmark($title);

        $actual = $fixture->run($callables);
        $lines = explode(PHP_EOL, $actual);

        foreach ($expectedLines as $index => $line) {
            self::assertSame($line, $lines[$index]);
        }
        foreach ($callables as $key => $callable) {
            $index++;
            if (is_int($key)) {
                /** @var string $key */
                $key = $callable;
            }
            self::assertSame(3, strpos($lines[$index], $key));
        }
    }

    public static function noOp(mixed $value): void
    {
    }

    /**
     * @return array<int,array<string,string|float>>
     */
    private function extractResults(string $results): array
    {
        if (empty($results)) {
            return [];
        }

        $output = [];
        $lines = explode(PHP_EOL, rtrim($results));
        foreach ($lines as $line) {
            $parts = explode(Benchmark::SEPARATOR, $line);
            $output[] = [
                'method' => $parts[1],
                'average' => Formatter::toDecimal($parts[2]),
                'minimum' => Formatter::toDecimal($parts[3]),
                'maximum' => Formatter::toDecimal($parts[4]),
                'chopped' => Formatter::toDecimal($parts[5]),
            ];
        }

        return $output;
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    private function getBenchmark(string $title = 'tester', int $samples = 1): Benchmark
    {
        $iterations = 1;
        /** @var Benchmark $benchmark */
        $benchmark = $this->container->get(Benchmark::class, [$samples, $iterations, $title]);

        return $benchmark;
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    private function setTimerDurations(): void {
        /** @var FixedTimer $timer */
        $timer = $this->container->get(TimerInterface::class);
        $timer->setDuration(self::TIMER_DURATION);
        $this->container->set(TimerInterface::class, $timer);
    }
}
