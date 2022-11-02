<?php

declare(strict_types=1);

namespace MtsBenchmarks\Helpers;

use MtsBenchmarks\Benchmark;
use MtsDependencyInjection\Container;

class ContainerFactory
{
    public static function create(): Container
    {
        $container = new Container();
        $container->load([
            Calculate::class,
            Benchmark::class,
        ]);

        return $container;
    }
}
