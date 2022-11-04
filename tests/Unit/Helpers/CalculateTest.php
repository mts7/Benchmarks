<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Unit\Helpers;

use MtsBenchmarks\Helper\Calculate;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
final class CalculateTest extends TestCase
{
    private Calculate $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new Calculate();
    }

    /**
     * @dataProvider averageData
     */
    public function testAverage(array $values, float $expected): void
    {
        $actual = $this->fixture->average($values);

        self::assertSame($expected, $actual);
    }

    public function averageData(): iterable
    {
        yield '2, 4, 6, 7' => [
            'values' => [2, 4, 6, 7],
            'expected' => 4.75,
        ];

        yield '1, 2, 4, 6, 8' => [
            'values' => [1, 2, 4, 6, 8],
            'expected' => 4.2,
        ];

        yield '1, 3, 8' => [
            'values' => [1, 3, 8],
            'expected' => 4.0,
        ];

        yield '1, 3' => [
            'values' => [1, 3],
            'expected' => 2.0,
        ];
    }

    /**
     * @dataProvider choppedAverageData
     */
    public function testChoppedAverage(array $values, float $expected): void
    {
        $actual = $this->fixture->choppedAverage($values);

        self::assertSame($expected, $actual);
    }

    public function choppedAverageData(): iterable
    {
        yield '7, 4, 6, 2' => [
            'values' => [7, 4, 6, 2],
            'expected' => 5.0,
        ];

        yield '6, 2, 1, 4, 8' => [
            'values' => [6, 2, 1, 4, 8],
            'expected' => 4.0,
        ];

        yield '1, 3, 8' => [
            'values' => [1, 3, 8],
            'expected' => 3.0,
        ];

        yield '1, 3' => [
            'values' => [1, 3],
            'expected' => 2.0,
        ];
    }
}
