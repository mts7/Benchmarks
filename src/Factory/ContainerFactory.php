<?php

declare(strict_types=1);

namespace MtsBenchmarks\Factory;

use MtsBenchmarks\Benchmark;
use MtsBenchmarks\Helper\Calculator;
use MtsBenchmarks\Helper\IncrementIntegerIterator;
use MtsBenchmarks\Report\ConsoleReport;
use MtsDependencyInjection\Container;
use MtsTimer\Timer;
use MtsTimer\TimerInterface;

/**
 * This factory creates a Container and contains the specifications needed.
 *
 * As more classes are introduced to the application, add their interfaces,
 * aliases, or fully-qualified class names with or without their concretions to
 * the getConfig array. Container should report any invalid usage.
 */
class ContainerFactory
{
    /**
     * Creates a Container with the provided config array.
     *
     * When $config is empty, create uses the default configuration from getConfig.
     *
     * @param array<int|class-string|string,class-string|object|null> $config
     *  Valid array for loading classes into Container
     */
    public static function create(array $config = []): Container
    {
        if (empty($config)) {
            $config = self::getConfig();
        }
        $container = new Container();
        $container->load($config);

        return $container;
    }

    /**
     * Gets a pre-defined configuration for this project.
     *
     * @return array<int|class-string|string,class-string|object|null>
     */
    public static function getConfig(): array
    {
        return [
            Benchmark::class,
            Calculator::class,
            ConsoleReport::class,
            IncrementIntegerIterator::class,
            TimerInterface::class => Timer::class,
        ];
    }
}
