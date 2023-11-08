<?php

declare(strict_types=1);

namespace MtsBenchmarks\Helper;

use Iterator;

/**
 * This iterator replaces the standard incrementing for loop.
 *
 * The key and current (value) are the same.
 * Example usage:
 *      $iterator = new IncrementIntegerIterator($iterations);
 *      foreach ($iterator as $key => $value) {
 *          echo "{$key} => {$value}" . PHP_EOL;
 *      }
 *
 * @implements \Iterator<int>
 */
class IncrementIntegerIterator implements Iterator
{
    private int $iterations;

    private int $pointer = 0;

    public function __construct(int $iterations)
    {
        $this->iterations = $iterations;
    }

    public function current(): int
    {
        return $this->pointer;
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function next(): void
    {
        $this->pointer++;
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }

    public function valid(): bool
    {
        return $this->pointer < $this->iterations;
    }
}
