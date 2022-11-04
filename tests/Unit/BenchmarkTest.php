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
     * @dataProvider runData
     *
     * @param array<int|string,callable> $callables
     * @param array<string,array<int,float>> $expected
     *
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \MtsTimer\Exception\IncompleteTimingException
     * @throws \ReflectionException
     */
    public function testRun(array $callables, array $expected): void
    {
        $this->setTimerDurations();
        $fixture = $this->getBenchmark();

        $actual = $fixture->run($callables);

        foreach ($actual as $name => $durations) {
            $this->assertSame($expected[$name][0], round($durations[0], 1));
        }
    }

    /**
     * @return iterable<string,array<string,array<int|string,string|callable|array<int,float>>>>
     */
    public function runData(): iterable
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

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    private function getBenchmark(int $samples = 1): Benchmark
    {
        $iterations = 1;
        /** @var Benchmark $benchmark */
        $benchmark = $this->container->get(Benchmark::class, [$samples, $iterations]);

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
