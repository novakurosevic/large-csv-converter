<?php

namespace Noki\LargeCsvConverter\Tests;

use Noki\LargeCsvConverter\LargeCsvConverter;
use PHPUnit\Framework\TestCase;

class CsvToXlsxTest extends TestCase
{
    private string $tempCsv;
    private string $tempXlsx;

    protected function setUp(): void
    {
        $this->tempCsv = sys_get_temp_dir() . '/test_csv_' . uniqid() . '.csv';
        $this->tempXlsx = sys_get_temp_dir() . '/test_xlsx_' . uniqid() . '.xlsx';

        file_put_contents($this->tempCsv, "Name,Email,Age\nJohn,john@example.com,30\nAna,ana@example.com,25");
    }

    protected function tearDown(): void
    {
        @unlink($this->tempCsv);
        @unlink($this->tempXlsx);
    }

    public function testCsvToXlsxCreatesFile(): void
    {
        LargeCsvConverter::csvToXlsx(
            csv_file_path: $this->tempCsv,
            excel_file_path: $this->tempXlsx
        );

        $this->assertFileExists($this->tempXlsx);
        $this->assertGreaterThan(100, filesize($this->tempXlsx));
    }

    public function testCsvToXlsxWithCustomOptions(): void
    {
        LargeCsvConverter::csvToXlsx(
            csv_file_path: $this->tempCsv,
            excel_file_path: $this->tempXlsx,
            first_line_is_header: false,
            delimiter: ',',
            enclosure: '"',
            encoding: 'UTF-8'
        );

        $this->assertFileExists($this->tempXlsx);
    }
}
