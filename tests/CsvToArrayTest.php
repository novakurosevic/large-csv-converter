<?php

namespace Noki\LargeCsvConverter\Tests;

use Noki\LargeCsvConverter\LargeCsvConverter;
use PHPUnit\Framework\TestCase;

class CsvToArrayTest extends TestCase
{
    private string $tempCsv;

    protected function setUp(): void
    {
        $this->tempCsv = sys_get_temp_dir() . '/test_csv_' . uniqid() . '.csv';
        file_put_contents($this->tempCsv, "Name,Email\nAlice,alice@example.com\nBob,bob@example.com");
    }

    protected function tearDown(): void
    {
        @unlink($this->tempCsv);
    }

    public function testCsvToArrayReturnsArray(): void
    {
        $data = LargeCsvConverter::csvToArray(csv_file_path: $this->tempCsv);
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertEquals('Alice', $data[0]['Name']);
    }

    public function testCsvToArrayThrowsErrorForMissingFile(): void
    {
        $this->expectException(\RuntimeException::class);
        LargeCsvConverter::csvToArray(csv_file_path: '/nonexistent/file.csv');
    }
}

