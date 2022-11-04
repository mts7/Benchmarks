<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Mock;

/**
 * Test class that exists for mocking purposes.
 *
 * @psalm-suppress PossiblyUnusedMethod
 */
class Blank
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function noOp(mixed $value): mixed
    {
        // provide a method that does nothing
        return $value;
    }
}
