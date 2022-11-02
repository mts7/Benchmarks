<?php

declare(strict_types=1);

namespace MtsBenchmarks\Helper;

use MtsBenchmarks\Benchmark;
use MtsDependencyInjection\Container;
use MtsTimer\Timer;
use MtsTimer\TimerInterface;

class ContainerFactory
{
    public static function create(): Container
    {
        $container = new Container();
        $container->load([
            TimerInterface::class => Timer::class,
            Calculate::class,
            Benchmark::class,
        ]);

        return $container;
    }
}
