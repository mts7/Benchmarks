<?php

declare(strict_types=1);

namespace MtsBenchmarks\Tests\Integration\Report;

use Generator;
use MtsBenchmarks\Helper\Calculator;
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

    protected function setUp(): void
    {
        parent::setUp();

        $calculator = new Calculator();

        $report = new ConsoleReport($calculator);
        $this->fixture = $report;
    }

    /**
     * @dataProvider buildHeadersData
     */
    public function testBuildHeaders(string $title, string $expectedTitle): void
    {
        $expected =
            " | {$expectedTitle} | Average           | Minimum           | Maximum           | Chopped Average   | ";
        $expected .= PHP_EOL . str_repeat('=', 103) . PHP_EOL;

        $actual = $this->fixture->buildHeaders($title);

        $this->assertSame($expected, $actual);
    }

    public static function buildHeadersData(): iterable
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

        $lines = explode(PHP_EOL, trim($actual, PHP_EOL));
        foreach ($lines as $index => $line) {
            $this->assertSame($expected[$index]['string'], $line);
        }

        foreach ($expectedResults as $index => $result) {
            $this->assertSame($expected[$index]['method'], $result['method']);
            // verify the time matches
            $this->assertSame($expected[$index]['average'], $result['average']);
            $this->assertSame($expected[$index]['minimum'], $result['minimum']);
            $this->assertSame($expected[$index]['maximum'], $result['maximum']);
            $this->assertSame($expected[$index]['chopped'], $result['chopped']);
        }
    }

    /**
     * @return \Generator<string,array<string,array<int|string,array<int|string,float|string>>>>
     */
    public static function buildResultsData(): Generator
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
        $values = '| 0.1281365         | 0.1152345         | 0.1452345         | 0.127670277777... | ';

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
                    'string' => ' | noOp              ' . $values,
                    'method' => 'noOp             ',
                    'average' => '0.1281365        ',
                    'minimum' => '0.1152345        ',
                    'maximum' => '0.1452345        ',
                    'chopped' => '0.127670277777... |',
                ],
            ],
        ];

        yield 'two results' => [
            'results' => [
                'noOp1' => $results,
                'noOp2' => $results,
            ],
            'expected' => [
                [
                    'string' => ' | noOp1             ' . $values,
                    'method' => 'noOp1            ',
                    'average' => '0.1281365        ',
                    'minimum' => '0.1152345        ',
                    'maximum' => '0.1452345        ',
                    'chopped' => '0.127670277777...',
                ],
                [
                    'string' => ' | noOp2             ' . $values,
                    'method' => 'noOp2            ',
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

    public function testGenerate(): void
    {
        $samples = 1;
        $iterations = 1;
        $title = __FUNCTION__;
        $results = ['callable name' => [1, 1.5, 1.25, 2, 1.125, 1.625, 1.5, 2.125]];
        $expected = 'Benchmarking testGenerate over 1 iterations with 1 samples

 | testGenerate      | Average           | Minimum           | Maximum           | Chopped Average   | 
=======================================================================================================
 | callable name     | 1.515625          | 1.                | 2.125             | 1.5               | 
';

        $actual = $this->fixture->generate($samples, $iterations, $title, $results);

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
