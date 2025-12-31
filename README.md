# Large CSV Converter

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE.md)
**Laravel package for fast and memory-efficient CSV and Excel conversions.**

This package allows you to easily convert large CSV files to XLSX or JSON format, and vice versa — all while keeping low memory usage thanks to the excellent [OpenSpout](https://github.com/openspout/openspout) library.

---

## 🚀 Features

* Convert **CSV → XLSX**, **XLSX → CSV**, and **CSV → JSON**
* Stream-based processing for **very large files**
* **Memory-safe** even for multi-gigabyte CSVs
* Detects and handles **headers automatically**
* Fully compatible with **Laravel** (auto-discovery supported)
* Works on **Linux**, **macOS**, and **Windows**

---

## 🧩 Requirements

* PHP ≥ 8.1
* Laravel ≥ 9.x
* PHP extensions:

    * `ext-zip`
    * `ext-xmlreader`

---

## ⚙️ Installation

Install the package via Composer:

```bash
composer require noki/large-csv-converter
```

Laravel will automatically register the service provider and alias.

---

## 🧠 Basic Usage

Import the class:

```php
use Noki\LargeCsvConverter\LargeCsvConverter;
```

### 🟢 Example 1: Convert CSV → XLSX (simple)

```php
use Noki\LargeCsvConverter\LargeCsvConverter;

LargeCsvConverter::csvToXlsx(
    csv_file_path: '/var/www/html/large-csv/test.csv',
    excel_file_path: '/var/www/html/large-csv/test.xlsx',
);
```

This reads `test.csv` and creates `test.xlsx` in the specified directory.
If the first row of the CSV is a header, it will be preserved automatically.

---

## 🧩 Advanced Usage (with optional parameters)

You can customize delimiters, encoding, and whether the first line is a header:

```php
use Noki\LargeCsvConverter\LargeCsvConverter;

LargeCsvConverter::csvToXlsx(
    csv_file_path: '/var/www/html/large-csv/test.csv',
    excel_file_path: '/var/www/html/large-csv/test.xlsx',
    first_line_is_header: true,       // Default: true
    delimiter: ';',                   // Default: ','
    enclosure: '"',                   // Default: '"'
    encoding: 'UTF-8',                // Default: 'UTF-8'
);
```

---

## 🔄 XLSX → CSV

Convert Excel files back to CSV:

```php
use Noki\LargeCsvConverter\LargeCsvConverter;

LargeCsvConverter::xlsxToCsv(
    csv_file_path: '/var/www/html/large-csv/output.csv',
    excel_file_path: '/var/www/html/large-csv/input.xlsx',
);
```

You can again customize options:

```php
use Noki\LargeCsvConverter\LargeCsvConverter;

LargeCsvConverter::xlsxToCsv(
    csv_file_path: '/var/www/html/large-csv/output.csv',
    excel_file_path: '/var/www/html/large-csv/input.xlsx',
    first_line_is_header: true,   // Default: true
    delimiter: ',',               // Default: ','
    enclosure: '"'                // Default: '"'
);
```

---

## 📄 CSV → JSON

Convert large CSVs directly into JSON files:

```php
use Noki\LargeCsvConverter\LargeCsvConverter;

LargeCsvConverter::csvToJson(
    csv_file_path: '/var/www/html/large-csv/test.csv',
    json_file_path: '/var/www/html/large-csv/test.json',
);
```

Advanced example:

```php
use Noki\LargeCsvConverter\LargeCsvConverter;

LargeCsvConverter::csvToJson(
    csv_file_path: '/var/www/html/large-csv/test.csv',
    json_file_path: '/var/www/html/large-csv/test.json',
    first_line_is_header: true,     // If CSV has header row
    delimiter: ',',                 // Field separator
    enclosure: '"',                 // Text enclosure
    encoding: 'UTF-8'               // File encoding
);
```

---

## 🧰 CSV → Array

Read CSV into PHP array (with header detection and memory checks):

```php
use Noki\LargeCsvConverter\LargeCsvConverter;

$array = LargeCsvConverter::csvToArray(
    csv_file_path: '/var/www/html/large-csv/test.csv'
);
```

Advanced usage:

```php
use Noki\LargeCsvConverter\LargeCsvConverter;

$array = LargeCsvConverter::csvToArray(
    csv_file_path: '/var/www/html/large-csv/test.csv',
    first_line_is_header: true,     // Default: true
    delimiter: ',',                 // Default: ','
    enclosure: '"',                 // Default: '"'
    encoding: 'UTF-8'               // Default: 'UTF-8'
);
```

---

## ⚠️ Memory Safety

`csvToArray()` automatically estimates average memory per row and throws an exception if your PHP memory limit is about to be exceeded.

If you need to handle huge files, increase `memory_limit` in your `php.ini`, for example:

```ini
memory_limit = 512M
```

---

## 📦 Dependencies and Licenses

This package is licensed under the [MIT License](LICENSE.md).

It uses the following open-source libraries:

* [OpenSpout](https://github.com/openspout/openspout) – MIT License
* [Box/Spout](https://github.com/box/spout) – Apache License 2.0

---

## 🧑‍💻 Author

**Novak Urošević**
GitHub: [@novakurosevic](https://github.com/novakurosevic)

---

## License

This package is licensed under the [MIT License](LICENSE.md).

It uses the following open-source libraries:

- [OpenSpout](https://github.com/openspout/openspout) – MIT License
- [Box/Spout](https://github.com/box/spout) – Apache License 2.0
