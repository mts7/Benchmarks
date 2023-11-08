<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Integration;

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Helper\Formatter;
use MtsBenchmarks\Helper\IncrementIntegerIterator;
use MtsBenchmarks\Tests\Mock\Blank;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->blankMock = $this->getMockBuilder(Blank::class)
            ->onlyMethods(['noOp'])
            ->getMock();
        $this->blankMock->method('noOp')->willReturnArgument(0);
    }

    /**
     * @dataProvider buildSamplesData
     */
    public function testBuildSamples(int $samples): void
    {
        $this->blankMock->expects($this->atLeastOnce())
            ->method('noOp');

        /** @var callable $callable */
        $callable = [$this->blankMock, 'noOp'];
        $fixture = $this->getBenchmark($samples);

        $durations = $fixture->buildSamples($callable);

        $this->assertCount($samples, $durations);
        $this->assertSame(
            Formatter::toDecimal(FixedTimer::DURATION),
            (string) round($durations[$samples - 1], 1)
        );
    }

    public static function buildSamplesData(): iterable
    {
        yield 'one' => [
            'samples' => 1,
        ];

        yield 'two' => [
            'samples' => 2,
        ];
    }

    /**
     * @dataProvider runData
     *
     * @param array<int|string,callable> $callables
     * @param array<string,array<int,float>> $expected
     */
    public function testRun(array $callables, array $expected): void
    {
        $fixture = $this->getBenchmark();

        $actual = $fixture->run($callables);

        foreach ($actual as $name => $durations) {
            $this->assertSame($expected[$name][0], round($durations[0], 1));
        }
    }

    /**
     * @return iterable<string,array<string,array<int|string,string|callable|array<int,float>>>>
     */
    public static function runData(): iterable
    {
        yield 'multiple methods' => [
            'callables' => [
                'noOp' => [new Blank(), 'noOp'],
                'is_int',
            ],
            'expected' => [
                'noOp' => [
                    FixedTimer::DURATION,
                ],
                'is_int' => [
                    FixedTimer::DURATION,
                ],
            ],
        ];
    }

    private function getBenchmark(int $samples = 1): Benchmark
    {
        $samplesIterator = new IncrementIntegerIterator($samples);
        $iterationsIterator = new IncrementIntegerIterator(1);
        $timer = $this->createMock(TimerInterface::class);
        $timer->method('getTotalDuration')
            ->willReturn(FixedTimer::DURATION);

        $timer->expects($this->atLeastOnce())
            ->method('reset');
        $timer->expects($this->atLeastOnce())
            ->method('start');
        $timer->expects($this->atLeastOnce())
            ->method('stop');
        $timer->expects($this->atLeastOnce())
            ->method('addDuration');

        return new Benchmark($samplesIterator, $iterationsIterator, $timer);
    }
}
