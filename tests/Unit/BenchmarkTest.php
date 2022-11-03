<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Unit;

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Factory\ContainerFactory;
use MtsBenchmarks\Helper\Formatter;
use MtsBenchmarks\Tests\Mock\Blank;
use MtsDependencyInjection\Container;
use MtsTimer\FixedTimer;
use MtsTimer\TimerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
final class BenchmarkTest extends TestCase
{
    private MockObject $blankMock;

    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = ContainerFactory::create();
        $this->container->set(TimerInterface::class, FixedTimer::class);

        $this->blankMock = $this->getMockBuilder(Blank::class)
            ->onlyMethods(['noOp'])
            ->getMock();
        $this->blankMock->method('noOp')->will($this->returnArgument(0));
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
        $expected =
            " | {$expectedTitle} | Average           | Minimum           | Maximum           | Chopped Average   | ";
        $expected .= PHP_EOL . str_repeat('=', 103) . PHP_EOL;
        $fixture = $this->getBenchmark($title);

        $actual = $fixture->buildHeaders();

        $this->assertSame($expected, $actual);
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
     * @throws \MtsTimer\Exception\IncompleteTimingException
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
            $this->assertSame($expected, $results);

            return;
        }

        foreach ($results as $index => $result) {
            $this->assertSame($expected[$index]['method'], $result['method']);
            // verify the time is greater than the minimum
            $this->assertSame(Formatter::toDecimal($expected[$index]['average']), $result['average']);
            $this->assertSame(Formatter::toDecimal($expected[$index]['minimum']), $result['minimum']);
            $this->assertSame(Formatter::toDecimal($expected[$index]['maximum']), $result['maximum']);
            $this->assertSame(Formatter::toDecimal($expected[$index]['chopped']), $result['chopped']);
            // verify all the results are the same since there is only 1 sample and 1 iteration
            $this->assertSame($result['average'], $result['minimum']);
            $this->assertSame($result['average'], $result['maximum']);
            $this->assertSame($result['average'], $result['chopped']);
        }
    }

    /**
     * @return iterable<string,array<string,array|callable>>
     */
    public function buildResultsData(): iterable
    {
        yield 'empty results' => [
            'methods' => [],
            'expected' => [],
        ];

        yield 'no Op once' => [
            'methods' => [
                'noOp1' => [new Blank(), 'noOp'],
            ],
            'expected' => [
                [
                    'method' => str_pad('noOp1', Benchmark::COLUMN_WIDTH),
                    'average' => FixedTimer::DURATION,
                    'minimum' => FixedTimer::DURATION,
                    'maximum' => FixedTimer::DURATION,
                    'chopped' => FixedTimer::DURATION,
                ],
            ],
        ];

        yield 'no Op twice' => [
            'methods' => [
                'noOp1' => [new Blank(), 'noOp'],
                'noOp2' => [new Blank(), 'noOp'],
            ],
            'expected' => [
                [
                    'method' => str_pad('noOp1', Benchmark::COLUMN_WIDTH),
                    'average' => FixedTimer::DURATION,
                    'minimum' => FixedTimer::DURATION,
                    'maximum' => FixedTimer::DURATION,
                    'chopped' => FixedTimer::DURATION,
                ],
                [
                    'method' => str_pad('noOp2', Benchmark::COLUMN_WIDTH),
                    'average' => FixedTimer::DURATION,
                    'minimum' => FixedTimer::DURATION,
                    'maximum' => FixedTimer::DURATION,
                    'chopped' => FixedTimer::DURATION,
                ],
            ],
        ];
    }

    /**
     * @dataProvider buildSamplesData
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \MtsTimer\Exception\IncompleteTimingException
     * @throws \ReflectionException
     */
    public function testBuildSamples(int $samples): void
    {
        $this->blankMock->expects($this->atLeastOnce())
            ->method('noOp');

        $this->setTimerDurations();
        $title = __FUNCTION__;
        /** @var callable $callable */
        $callable = [$this->blankMock, 'noOp'];
        $fixture = $this->getBenchmark($title, $samples);

        $durations = $fixture->buildSamples($callable);

        $this->assertCount($samples, $durations);
        $this->assertSame(
            Formatter::toDecimal(FixedTimer::DURATION),
            (string) round((float) $durations[$samples - 1], 1)
        );
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

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider titleData
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \MtsTimer\Exception\IncompleteTimingException
     * @throws \ReflectionException
     */
    public function testRun(string $title, string $expectedTitle): void
    {
        $this->blankMock->expects($this->atLeastOnce())
            ->method('noOp');

        $this->setTimerDurations();
        /** @var array<int|string,callable> $callables */
        $callables = ['noOp' => [$this->blankMock, 'noOp'], 'is_int'];
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
            $this->assertSame($line, $lines[$index]);
        }
        foreach ($callables as $key => $callable) {
            $index++;
            if (is_int($key)) {
                /** @var string $key */
                $key = $callable;
            }
            $this->assertSame(3, strpos($lines[$index], $key));
        }
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
    private function setTimerDurations(): void
    {
        /** @var FixedTimer $timer */
        $timer = $this->container->get(TimerInterface::class);
        $this->container->set(TimerInterface::class, $timer);
    }
}
