<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Unit\Report;

use Generator;
use MtsBenchmarks\Factory\ContainerFactory;
use MtsBenchmarks\Report\ConsoleReport;
use PHPUnit\Framework\TestCase;

/**
 * @group report
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
final class ConsoleReportTest extends TestCase
{
    private ConsoleReport $fixture;

    /**
     * @throws \MtsDependencyInjection\Exceptions\ContainerException
     * @throws \MtsDependencyInjection\Exceptions\MissingContainerDefinitionException
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $container = ContainerFactory::create();

        /** @var ConsoleReport $report */
        $report = $container->get(ConsoleReport::class);
        $this->fixture = $report;
    }

    /**
     * @dataProvider titleData
     */
    public function testBuildHeaders(string $title, string $expectedTitle): void
    {
        $expected =
            " | {$expectedTitle} | Average           | Minimum           | Maximum           | Chopped Average   | ";
        $expected .= PHP_EOL . str_repeat('=', 103) . PHP_EOL;

        $actual = $this->fixture->buildHeaders($title);

        $this->assertSame($expected, $actual);
    }

    public function titleData(): iterable
    {
        yield 'excessively-long title' => [
            'title' => md5(__FUNCTION__),
            'expectedTitle' => substr(md5(__FUNCTION__), 0, ConsoleReport::COLUMN_WIDTH - 3) . '...',
        ];

        yield 'maximum-length title' => [
            'title' => substr(md5(__FUNCTION__), 0, ConsoleReport::COLUMN_WIDTH),
            'expectedTitle' => substr(md5(__FUNCTION__), 0, ConsoleReport::COLUMN_WIDTH),
        ];

        yield 'short title' => [
            'title' => 'tester',
            'expectedTitle' => 'tester           ',
        ];
    }

    /**
     * @param array<string,array<int,float>> $results
     * @param array<int,array<string,string|int|float>> $expected
     *
     * @dataProvider buildResultsData
     */
    public function testBuildResults(array $results, array $expected): void
    {
        $actual = $this->fixture->buildResults($results);
        $expectedResults = $this->extractResults($actual);

        if (empty($expected)) {
            $this->assertSame($expected, $expectedResults);

            return;
        }

        foreach ($expectedResults as $index => $result) {
            $this->assertSame($expected[$index]['method'], $result['method']);
            // verify the time is greater than the minimum
            $this->assertSame($expected[$index]['average'], $result['average']);
            $this->assertSame($expected[$index]['minimum'], $result['minimum']);
            $this->assertSame($expected[$index]['maximum'], $result['maximum']);
            $this->assertSame($expected[$index]['chopped'], $result['chopped']);
        }
    }

    /**
     * @return \Generator<string,array<string,array<int|string,array<int|string,float|string>>>>
     */
    public function buildResultsData(): Generator
    {
        $results = [
            0.1252345,
            0.1272345,
            0.1352345,
            0.1252545,
            0.1268345,
            0.1252335,
            0.1152345,
            0.1452345,
            0.1356345,
            0.1241345,
            0.1242375,
        ];

        yield 'empty results' => [
            'results' => [],
            'expected' => [],
        ];

        yield 'single result' => [
            'results' => [
                'noOp' => $results,
            ],
            'expected' => [
                [
                    'method' => 'noOp             ',
                    'average' => '0.1281365        ',
                    'minimum' => '0.1152345        ',
                    'maximum' => '0.1452345        ',
                    'chopped' => '0.127670277777... |',
                ],
            ],
        ];
    }

    public function testBuildTitle(): void
    {
        $title = __FUNCTION__;
        $expected = "Benchmarking {$title} over 1 iterations with 1 samples" . PHP_EOL . PHP_EOL;

        $actual = $this->fixture->buildTitle(1, 1, $title);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<int,array<string,string|float>>
     */
    private function extractResults(string $results): array
    {
        if (empty($results)) {
            return [];
        }

        $output = [];
        $lines = explode(PHP_EOL, rtrim($results));
        foreach ($lines as $line) {
            $parts = explode(ConsoleReport::SEPARATOR, $line);
            $output[] = [
                'method' => $parts[1],
                'average' => $parts[2],
                'minimum' => $parts[3],
                'maximum' => $parts[4],
                'chopped' => $parts[5],
            ];
        }

        return $output;
    }
}
