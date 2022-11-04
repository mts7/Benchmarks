<?php

declare(strict_types=1);

namespace MtsBenchmarks\Report;

/**
 * All reports should contain ways to build title, headers, and results.
 */
interface ReportInterface
{
    public function buildTitle(int $samples, int $iterations, string $title): string;

    public function buildHeaders(string $title): string;

    /**
     * @param array<string,array<int,float>> $results
     */
    public function buildResults(array $results): string;
}
