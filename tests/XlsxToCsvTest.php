<?php

namespace Noki\LargeCsvConverter\Tests;

use Noki\LargeCsvConverter\LargeCsvConverter;
use PHPUnit\Framework\TestCase;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class XlsxToCsvTest extends TestCase
{
    private string $tempXlsx;
    private string $tempCsv;

    protected function setUp(): void
    {
        $this->tempXlsx = sys_get_temp_dir() . '/test_xlsx_' . uniqid() . '.xlsx';
        $this->tempCsv = sys_get_temp_dir() . '/test_csv_' . uniqid() . '.csv';

        // Create XLSX file with OpenSpout
        $writer = new Writer();
        $writer->openToFile($this->tempXlsx);
        $writer->addRow(Row::fromValues(['Name', 'Email', 'Age']));
        $writer->addRow(Row::fromValues(['Mark', 'mark@example.com', '40']));
        $writer->close();
    }

    protected function tearDown(): void
    {
        @unlink($this->tempXlsx);
        @unlink($this->tempCsv);
    }

    public function testXlsxToCsvCreatesFile(): void
    {
        LargeCsvConverter::xlsxToCsv(
            csv_file_path: $this->tempCsv,
            excel_file_path: $this->tempXlsx
        );

        $this->assertFileExists($this->tempCsv);
        $content = file_get_contents($this->tempCsv);
        $this->assertStringContainsString('Mark', $content);
    }
}

