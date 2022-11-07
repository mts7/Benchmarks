<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Unit\Helpers;

use MtsBenchmarks\Factory\ContainerFactory;
use MtsBenchmarks\Helper\IncrementIntegerIterator;
use MtsDependencyInjection\Container;
use PHPUnit\Framework\TestCase;

/**
 * @group helper
 */
final class IncrementIntegerIteratorTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = ContainerFactory::create();
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testCurrent(): void
    {
        $fixture = $this->getIterator();
        $expected = 0;

        $actual = $fixture->current();

        $this->assertSame($expected, $actual);
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testKey(): void
    {
        $fixture = $this->getIterator();
        $expected = 0;

        $actual = $fixture->key();

        $this->assertSame($expected, $actual);
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testNext(): void
    {
        $fixture = $this->getIterator();
        $expected = 1;

        $fixture->next();
        $actual = $fixture->key();

        $this->assertSame($expected, $actual);
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testRewind(): void
    {
        $iterations = 5;
        $fixture = $this->getIterator($iterations);
        $expected = 0;

        $values = [];
        foreach ($fixture as $key => $value) {
            $values[$key] = $value;
        }
        $lastKey = $fixture->key();
        $fixture->rewind();
        $actual = $fixture->key();

        $this->assertSame($expected, $actual);
        $this->assertCount($iterations, $values);
        $this->assertSame($iterations, $lastKey);
        $this->assertSame(array_values($values), array_keys($values));
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    public function testValid(): void
    {
        $iterations = 3;
        $fixture = $this->getIterator($iterations);

        $fixture->next();
        $fixture->next();
        $fixture->next();
        $fixture->next();
        $actual = $fixture->valid();

        $this->assertFalse($actual);
    }

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    private function getIterator(int $iterations = 10): IncrementIntegerIterator
    {
        /** @var IncrementIntegerIterator $iterator */
        $iterator = $this->container->get(IncrementIntegerIterator::class, [$iterations]);

        return $iterator;
    }
}
