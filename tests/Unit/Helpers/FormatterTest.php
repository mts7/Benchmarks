<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Unit\Helpers;

use MtsBenchmarks\Helper\Formatter;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
final class FormatterTest extends TestCase
{
    /**
     * @dataProvider toDecimalData
     */
    public function testToDecimal(string|float|int $value, string $expected): void
    {
        $actual = Formatter::toDecimal($value);

        self::assertSame($expected, $actual);
    }

    public function toDecimalData(): iterable
    {
        yield 'zero' => [
            'value' => 0,
            'expected' => '0.',
        ];

        yield '1 divided by 3' => [
            'value' => 1 / 3,
            'expected' => '0.3333333333333333',
        ];
    }
}
