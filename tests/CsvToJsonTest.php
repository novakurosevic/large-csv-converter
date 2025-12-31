<?php

namespace Noki\LargeCsvConverter\Tests;

use Noki\LargeCsvConverter\LargeCsvConverter;
use PHPUnit\Framework\TestCase;

class CsvToJsonTest extends TestCase
{
    private string $tempCsv;
    private string $tempJson;

    protected function setUp(): void
    {
        $this->tempCsv = sys_get_temp_dir() . '/test_csv_' . uniqid() . '.csv';
        $this->tempJson = sys_get_temp_dir() . '/test_json_' . uniqid() . '.json';

        file_put_contents($this->tempCsv, "Name,Email\nSara,sara@example.com\nIvan,ivan@example.com");
    }

    protected function tearDown(): void
    {
        @unlink($this->tempCsv);
        @unlink($this->tempJson);
    }

    public function testCsvToJsonCreatesValidJsonFile(): void
    {
        LargeCsvConverter::csvToJson(
            csv_file_path: $this->tempCsv,
            json_file_path: $this->tempJson
        );

        $this->assertFileExists($this->tempJson);

        $json = json_decode(file_get_contents($this->tempJson), true);
        $this->assertIsArray($json);
        $this->assertCount(2, $json);
        $this->assertArrayHasKey('Email', $json[0]);
    }
}

