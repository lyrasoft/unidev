<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 .
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Excel;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Traversable;
use Windwalker\Filesystem\File;

/**
 * The ExcelImporter class.
 *
 * USAGE:
 * ```
 * $importer = new ExcelImporter('.../hello.xlsx');
 *
 * // Get All Data
 *
 * $importer->getSheetData([$sheet index or name]);
 * $importer->getAllData();
 *
 * // Each
 *
 * $importer->eachSheet(function ($item) { ... }, [$asValue = false], [$sheet index or name]);
 * $importer->eachAll(function ($sheet) { ... }, [$asValue = false]);
 *
 * // OR Use iterator
 *
 * foreach ($importer->getRowIterator([$asValue = false]) as $key => $item) { ... }
 *
 * foreach ($importer as $key => $item) { ... }
 *
 * foreach ($importer->getSheetsIterator() as $name => $sheet) {
 *     foreach ($sheet as $key => $item) { ... }
 * }
 * ```
 *
 * @since  1.5.13
 */
class ExcelImporter implements \IteratorAggregate
{
    /**
     * Property spreadsheet.
     *
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * Property startFromRow.
     *
     * @var  int
     */
    protected $startFrom = 1;

    /**
     * Property headerAsField.
     *
     * @var  bool
     */
    protected $headerAsField = true;

    /**
     * ExcelImporter constructor.
     *
     * @param string|null $file
     * @param string|null $format
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function __construct(?string $file = null, string $format = null)
    {
        if (strlen($file) < PHP_MAXPATHLEN && is_file($file)) {
            $this->loadFile($file, $format);
        } elseif ($file) {
            $this->load($file, $format);
        }
    }

    /**
     * loadFile
     *
     * @param string      $file
     * @param string|null $format
     *
     * @return  ExcelImporter
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     * @since  1.5.13
     */
    public function loadFile(string $file, ?string $format = null): self
    {
        $format = $format ?? File::getExtension($file);

        $reader = $this->createReader($format);

        $this->spreadsheet = $reader->load($file);

        return $this;
    }

    /**
     * getSheetIterator
     *
     * @param bool            $asValue
     * @param int|string|null $sheet
     *
     * @return  \Generator
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @since  1.5.13
     */
    public function getRowIterator(bool $asValue = false, $sheet = null): \Generator
    {
        if (is_int($sheet)) {
            $worksheet = $this->spreadsheet->getSheet($sheet);
        } elseif (is_string($sheet)) {
            $worksheet = $this->spreadsheet->getSheetByName($sheet);
        } else {
            $worksheet = $this->spreadsheet->getActiveSheet();
        }

        return $this->iterateSheetRows($worksheet, $asValue);
    }

    /**
     * getColumnIterator
     *
     * @param bool            $asValue
     * @param int|string|null $sheet
     *
     * @return  \Generator
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @since  1.5.14
     */
    public function getColumnIterator(bool $asValue = false, $sheet = null): \Generator
    {
        if (is_int($sheet)) {
            $worksheet = $this->spreadsheet->getSheet($sheet);
        } elseif (is_string($sheet)) {
            $worksheet = $this->spreadsheet->getSheetByName($sheet);
        } else {
            $worksheet = $this->spreadsheet->getActiveSheet();
        }

        return $this->iterateSheetColumns($worksheet, $asValue);
    }

    /**
     * getSheetsIterator
     *
     * @param bool $asValue
     *
     * @return  \Generator
     *
     * @since  1.5.13
     */
    public function getSheetsIterator(bool $asValue = false): ?\Generator
    {
        $loop = function () use ($asValue) {
            $sheets = $this->spreadsheet->getAllSheets();

            foreach ($sheets as $sheet) {
                yield $sheet->getTitle() => $this->iterateSheetRows($sheet, $asValue);
            }
        };

        return $loop();
    }

    /**
     * getSheetData
     *
     * @param int|string|null $sheet
     *
     * @return  array
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @since  1.5.13
     */
    public function getSheetData($sheet = null): array
    {
        return iterator_to_array($this->getRowIterator(true, $sheet));
    }

    /**
     * getAlldata
     *
     * @return  array
     *
     * @since  1.5.13
     */
    public function getAllData(): array
    {
        return array_map(
            'iterator_to_array',
            iterator_to_array($this->getSheetsIterator(true))
        );
    }

    /**
     * iterateSheet
     *
     * @param Worksheet $sheet
     * @param bool      $asValue
     *
     * @return  \Generator|null
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @since  1.5.13
     */
    protected function iterateSheetRows(Worksheet $sheet, bool $asValue = false): ?\Generator
    {
        $fields = [];

        foreach ($sheet->getRowIterator() as $i => $row) {
            if ($i < $this->startFrom) {
                continue;
            }

            // First row
            if ($i === $this->startFrom && $this->headerAsField) {
                // Prepare fields title
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $fields[$cell->getColumn()] = $col = $cell->getFormattedValue();

                    if ($col === '') {
                        $fields[$cell->getColumn()] = $cell->getColumn();
                    }
                }

                continue;
            }

            $item         = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $column = $this->headerAsField
                    ? $fields[$cell->getColumn()]
                    : $cell->getColumn();

                if ($asValue) {
                    $item[$column] = $cell->getFormattedValue();
                } else {
                    $item[$column] = $cell;
                }
            }

            yield $i => $item;
        }
    }

    /**
     * iterateSheetColumns
     *
     * @param Worksheet $sheet
     * @param bool      $asValue
     *
     * @return  \Generator|null
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @since  1.5.14
     */
    protected function iterateSheetColumns(Worksheet $sheet, bool $asValue = false): ?\Generator
    {
        $fields = [];

        foreach ($sheet->getColumnIterator() as $f => $row) {
            $i = Coordinate::columnIndexFromString($f);

            if ($i < $this->startFrom) {
                continue;
            }

            // First row
            if ($i === $this->startFrom && $this->headerAsField) {
                // Prepare fields title
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $fields[$cell->getRow()] = $col = $cell->getFormattedValue();

                    if ($col === '') {
                        $fields[$cell->getRow()] = $cell->getRow();
                    }
                }
            }

            $item         = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $column = $this->headerAsField
                    ? $fields[$cell->getRow()]
                    : $cell->getRow();

                if ($asValue) {
                    $item[$column] = $cell->getFormattedValue();
                } else {
                    $item[$column] = $cell;
                }
            }

            yield $f => $item;
        }
    }

    /**
     * eachSheet
     *
     * @param callable        $handler
     * @param bool            $asValue
     * @param int|string|null $sheet
     *
     * @return  void
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @since  1.5.13
     */
    public function eachSheet(callable $handler, bool $asValue = false, $sheet = null): void
    {
        foreach ($this->getRowIterator($asValue, $sheet) as $key => $item) {
            $handler($item, $key);
        }
    }

    /**
     * eachAll
     *
     * @param callable $handler
     * @param bool     $asValue
     *
     * @return  void
     *
     * @since  1.5.13
     */
    public function eachAll(callable $handler, bool $asValue = false): void
    {
        /** @var \Generator $sheet */
        foreach ($this->getSheetsIterator($asValue) as $sheet) {
            foreach ($sheet as $key => $item) {
                $handler($item, $key, $sheet);
            }
        }
    }

    /**
     * load
     *
     * @param string $data
     * @param string $format
     *
     * @return  ExcelImporter
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     * @since  1.5.13
     */
    public function load(string $data, string $format = 'Xlsx'): self
    {
        $temp = File::createTemp();

        File::write($temp, $data);

        $this->loadFile($temp, $format);

        File::delete($temp);

        return $this;
    }

    /**
     * createReader
     *
     * @param string|null $format
     *
     * @return  IReader
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     * @since  1.5.13
     */
    public function createReader(string $format = null): IReader
    {
        $format = $format ?? 'xlsx';

        $reader = IOFactory::createReader(ucfirst($format));
        $reader->setReadDataOnly(true);

        return $reader;
    }

    /**
     * Method to set property ignoreHeader
     *
     * @param int $ignoreHeader
     *
     * @return  static  Return self to support chaining.
     *
     * @since  1.5.13
     *
     * @deprecated
     */
    public function ignoreHeader(bool $ignoreHeader): self
    {
        $this->startFrom = (int) $ignoreHeader;

        return $this;
    }

    /**
     * startFromRow
     *
     * @param int $start
     *
     * @return  static
     *
     * @since  1.5.14
     */
    public function startFrom(int $start): self
    {
        $this->startFrom = $start;

        return $this;
    }

    /**
     * Method to set property headerAsField
     *
     * @param bool $headerAsField
     *
     * @return  static  Return self to support chaining.
     *
     * @since  1.5.13
     */
    public function headerAsField(bool $headerAsField)
    {
        $this->headerAsField = $headerAsField;

        return $this;
    }

    /**
     * Retrieve an external iterator
     * @link  https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getIterator(): Traversable
    {
        return $this->getRowIterator(true);
    }

    /**
     * Method to get property Spreadsheet
     *
     * @return  Spreadsheet
     *
     * @since  1.5.14
     */
    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    /**
     * Method to set property spreadsheet
     *
     * @param Spreadsheet $spreadsheet
     *
     * @return  static  Return self to support chaining.
     *
     * @since  1.5.14
     */
    public function setSpreadsheet(Spreadsheet $spreadsheet): self
    {
        $this->spreadsheet = $spreadsheet;

        return $this;
    }
}
