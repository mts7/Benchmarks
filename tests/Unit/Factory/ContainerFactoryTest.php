<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Unit\Factory;

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Factory\ContainerFactory;
use MtsBenchmarks\Helper\Calculator;
use MtsBenchmarks\Helper\IncrementIntegerIterator;
use MtsBenchmarks\Report\ConsoleReport;
use MtsDependencyInjection\Container;
use MtsTimer\TimerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ContainerFactoryTest extends TestCase
{
    public function testCreateReturnsContainer(): void
    {
        $expected = Container::class;
        $container = ContainerFactory::create();

        $this->assertInstanceOf($expected, $container);
    }

    public function testCreateContainerWithConfig(): void
    {
        $key = uniqid('key-');
        $container = ContainerFactory::create([$key => new stdClass()]);

        $this->assertCount(1, $container->view());
        $this->assertTrue($container->has($key));
    }

    public function testCreateHasDefaultClasses(): void
    {
        $container = ContainerFactory::create();

        $this->assertCount(5, $container->view());
        $this->assertTrue($container->has(Benchmark::class));
        $this->assertTrue($container->has(Calculator::class));
        $this->assertTrue($container->has(ConsoleReport::class));
        $this->assertTrue($container->has(IncrementIntegerIterator::class));
        $this->assertTrue($container->has(TimerInterface::class));
    }

    public function testGetConfig(): void
    {
        $config = ContainerFactory::getConfig();

        $this->assertCount(5, $config);
    }
}
