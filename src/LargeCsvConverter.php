<?php

namespace Noki\LargeCsvConverter;

use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\CSV\Options as CsvReaderOptions;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Writer\CSV\Options as CsvWriterOptions;
use OpenSpout\Common\Entity\Row;

class LargeCsvConverter
{
    /**
     * @param string $csv_file_path
     * @param string $excel_file_path
     * @param bool $first_line_is_header
     * @param string $delimiter
     * @param string $enclosure
     * @param string $encoding
     * @return void
     * @throws IOException
     * @throws ReaderNotOpenedException
     * @throws WriterNotOpenedException
     *
     */
    public static function csvToXlsx(
        string $csv_file_path = '',
        string $excel_file_path = '',
        bool $first_line_is_header = true,
        string $delimiter = ',',
        string $enclosure = '"',
        string $encoding = 'UTF-8'
    ): void
    {
        // Check does file exist
        if (!file_exists($csv_file_path)) {
            throw new \RuntimeException("Error reading CSV file from path: {$csv_file_path}");
        }
        if (empty($excel_file_path)) {
            throw new \RuntimeException("Excel file path not set.");
        }

        $options = new CsvReaderOptions(
            FIELD_DELIMITER: $delimiter,
            FIELD_ENCLOSURE: $enclosure,
            ENCODING: $encoding,
        );

        // Create reader with options and open CSV files
        $reader = new CsvReader($options);
        $reader->open($csv_file_path);

        // Write XLSX file
        $writer = new XLSXWriter();
        $writer->openToFile($excel_file_path); // write data to a file or to a PHP stream

        $is_first_line = true;
        $header = [];

        // Do line by line
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $data = $row->toArray(); // convert into array

                // Crate header
                if($is_first_line && $first_line_is_header) {
                    $is_first_line = false;
                    $header = $data;
                    $rowFromValues = Row::fromValues($header);
                    $writer->addRow($rowFromValues);
                    continue;
                }elseif ($is_first_line && !$first_line_is_header && empty($header)) {
                    $is_first_line = false;
                    $number_of_columns = count($data);
                    for($i = 0; $i < $number_of_columns; $i++) {
                        $header[] = 'Column' . ($i+1);
                    }
                    $rowFromValues = Row::fromValues($data);
                    $writer->addRow($rowFromValues);
                    continue;
                }

                // Add a row from an array of values
                $rowFromValues = Row::fromValues($data);
                $writer->addRow($rowFromValues);

            }
        }

        $writer->close();
        $reader->close();

        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            // Linux/Unix/Mac OS
            chmod($excel_file_path, 0666);
        }

    }

    /**
     * @param string $excel_file_path
     * @param string $csv_file_path
     * @param bool $first_line_is_header
     * @param string $delimiter
     * @param string $enclosure
     * @return void
     * @throws IOException
     * @throws ReaderNotOpenedException
     * @throws WriterNotOpenedException
     */
    public static function xlsxToCsv(
        string $csv_file_path = '',
        string $excel_file_path = '',
        bool $first_line_is_header = true,
        string $delimiter = ',',
        string $enclosure = '"'
    ): void
    {
        // Check does file exist
        if (!file_exists($excel_file_path)) {
            throw new \RuntimeException("Error reading XLSX file from path: {$excel_file_path}");
        }
        if (empty($csv_file_path)) {
            throw new \RuntimeException("CSV file path not set.");
        }

        // Open XLSX file
        $reader = new XLSXReader();
        $reader->open($excel_file_path);

        // Setting CSV writer
        $options = new CsvWriterOptions(
            FIELD_DELIMITER: $delimiter,
            FIELD_ENCLOSURE: $enclosure,
        );

        $writer = new CSVWriter($options);
        $writer->openToFile($csv_file_path);

        $is_first_line = true;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $data = $row->toArray();

                // Header set
                if ($is_first_line && $first_line_is_header) {
                    $is_first_line = false;
                    $writer->addRow(Row::fromValues($data));
                    continue;
                } elseif ($is_first_line && !$first_line_is_header) {
                    $is_first_line = false;
                    $writer->addRow(Row::fromValues($data));
                    continue;
                }

                // Other lines
                $writer->addRow(Row::fromValues($data));
            }
        }

        $writer->close();
        $reader->close();

        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            // Linux/Unix/Mac OS
            chmod($csv_file_path, 0666);
        }

    }


    /**
     * @param string $csv_file_path
     * @param bool $first_line_is_header
     * @param string $delimiter
     * @param string $enclosure
     * @param string $encoding
     * @return array
     * @throws IOException
     * @throws ReaderNotOpenedException
     */
    public static function csvToArray(
        string $csv_file_path = '',
        bool $first_line_is_header = true,
        string $delimiter = ',',
        string $enclosure = '"',
        string $encoding = 'UTF-8'
    ):array
    {
        // Check does file exist
        if (!file_exists($csv_file_path)) {
            throw new \RuntimeException("Error reading CSV file from path: {$csv_file_path}");
        }

        $limit = ini_get("memory_limit");
        $limit_in_bytes = self::convertToBytes($limit) - 100 * 1024; // Memory limit - 10Kb

        $options = new CsvReaderOptions(
            FIELD_DELIMITER: $delimiter,
            FIELD_ENCLOSURE: $enclosure,
            ENCODING: $encoding,
        );

        // Create reader with options and open CSV files
        $reader = new CsvReader($options);
        $reader->open($csv_file_path);

        $is_first_line = true;
        $memory_per_row_set = false;
        $header = [];
        $header_elements = 0;
        $maximum_rows = 0;
        $result_array = [];

        // Do line by line
        foreach ($reader->getSheetIterator() as $sheet) {
            $row_counter = 0;

            foreach ($sheet->getRowIterator() as $row) {
                $row_counter++;
                $data = $row->toArray(); // convert into array

                // Crate header
                if($is_first_line && $first_line_is_header) {
                    $is_first_line = false;
                    $header = $data;
                    $header_elements = count($header);
                    continue;
                }elseif ($is_first_line && !$first_line_is_header && empty($header)) {
                    $is_first_line = false;
                    $number_of_columns = count($data);
                    for($i = 0; $i < $number_of_columns; $i++) {
                        $header[] = 'Column' . ($i+1);
                    }
                    $header_elements = count($header);
                    continue;
                }

                if ($header_elements === count($data)) {
                    $result_array[] = array_combine($header, $data);
                } else {
                    $result_array[] = $data;
                }

                // Average memory per row
                if ( ($row_counter % 100 === 0) && !$memory_per_row_set) {
                    $memory_per_row_set = true;
                    $memory_per_row = memory_get_usage(true) / 100;
                    $maximum_rows = (int) floor($limit_in_bytes / $memory_per_row);
                }

                if( ($row_counter > $maximum_rows) && ($maximum_rows > 0) ){
                    throw new \RuntimeException("Could not create array. Memory limit of {$limit} exceeded.
                    Extend memory_limit in php.ini.");
                }



            }
        }

        $reader->close();

        return $result_array;

    }

    /**
     * @param $value
     * @return int
     */
    private static function convertToBytes($value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);
        $value = (int)$value;

        switch($last) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }

        return $value;
    }

    /**
     * @param string $csv_file_path
     * @param string $json_file_path
     * @param bool $first_line_is_header
     * @param string $delimiter
     * @param string $enclosure
     * @param string $encoding
     * @return void
     * @throws IOException
     * @throws ReaderNotOpenedException
     * @throws \ErrorException
     */
    public static function csvToJson(
        string $csv_file_path = '',
        string $json_file_path = '',
        bool $first_line_is_header = true,
        string $delimiter = ',',
        string $enclosure = '"',
        string $encoding = 'UTF-8'
    ): void
    {
        // Check does CSV file exist
        if (!file_exists($csv_file_path)) {
            throw new \RuntimeException("Error reading CSV file from path: {$csv_file_path}");
        }
        if (empty($json_file_path)) {
            throw new \RuntimeException("JSON file path not set.");
        }

        $options = new CsvReaderOptions(
            FIELD_DELIMITER: $delimiter,
            FIELD_ENCLOSURE: $enclosure,
            ENCODING: $encoding,
        );

        $reader = new CsvReader($options);
        $reader->open($csv_file_path);

        $json_file = fopen($json_file_path, 'w');
        fwrite($json_file, "[\n");

        $is_first_line = true;
        $is_first_object = true;
        $header = [];
        $header_elements = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $csv_row_data = $row->toArray();

                // Header, first line of csv
                if ($is_first_line && $first_line_is_header) {
                    $is_first_line = false;
                    $header = $csv_row_data;
                    $header_elements = count($header);
                    continue;
                } elseif ($is_first_line && !$first_line_is_header && empty($header)) {
                    $is_first_line = false;
                    $number_of_columns = count($csv_row_data);
                    for ($i = 0; $i < $number_of_columns; $i++) {
                        $header[] = 'Column' . ($i + 1);
                    }
                    $header_elements = count($header);
                }

                // Check number of columns
                if ($header_elements === count($csv_row_data)) {
                    $json_data = array_combine($header, $csv_row_data);
                } else {
                    throw new \ErrorException(
                        "CSV file is not valid. Different number of header elements and number of columns."
                    );
                }

                // Write comma only if this is not the first object
                if (!$is_first_object) {
                    fwrite($json_file, ",\n");
                } else {
                    $is_first_object = false;
                }

                fwrite($json_file, json_encode($json_data, JSON_UNESCAPED_UNICODE));
            }
        }

        fwrite($json_file, "\n]\n");

        $reader->close();
        fclose($json_file);

        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            // Linux/Unix/Mac OS
            chmod($json_file_path, 0666);
        }

    }

}
