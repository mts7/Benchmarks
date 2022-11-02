<?php

declare(strict_types=1);

namespace MtsBenchmarks\Helpers;

class Formatter
{
    public static function toDecimal(string|float|int $value, int $places = 16): string
    {
        return rtrim(sprintf('%.' . $places . 'f', $value), '0');
    }
}
