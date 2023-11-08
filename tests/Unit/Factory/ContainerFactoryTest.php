<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Unit\Factory;

use MtsBenchmarks\Factory\ContainerFactory;
use PHPUnit\Framework\TestCase;

final class ContainerFactoryTest extends TestCase
{
    public function testGetConfig(): void
    {
        $config = ContainerFactory::getConfig();

        $this->assertCount(5, $config);
    }
}
