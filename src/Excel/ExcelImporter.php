<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 .
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Excel;

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
 * @since  __DEPLOY_VERSION__
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
     * Property ignoreHeader.
     *
     * @var  bool
     */
    protected $ignoreHeader = true;

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
     * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
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

        return $this->iterateSheet($worksheet, $asValue);
    }

    /**
     * getSheetsIterator
     *
     * @param bool $asValue
     *
     * @return  \Generator
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getSheetsIterator(bool $asValue = false): ?\Generator
    {
        $loop = function () use ($asValue) {
            $sheets = $this->spreadsheet->getAllSheets();

            foreach ($sheets as $sheet) {
                yield $sheet->getTitle() => $this->iterateSheet($sheet, $asValue);
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
     * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
     */
    protected function iterateSheet(Worksheet $sheet, bool $asValue = false): ?\Generator
    {
        $fields = [];

        foreach ($sheet->getRowIterator() as $i => $row) {
            // First row
            if ($i === 1) {
                // Prepare fields title
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $fields[$cell->getColumn()] = $col = $cell->getFormattedValue();

                    if ($col === '') {
                        $fields[$cell->getColumn()] = $cell->getColumn();
                    }
                }

                // Ignore first row
                if ($this->ignoreHeader) {
                    continue;
                }
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
     * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
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
     * @param bool $ignoreHeader
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function ignoreHeader(bool $ignoreHeader)
    {
        $this->ignoreHeader = $ignoreHeader;

        return $this;
    }

    /**
     * Method to set property headerAsField
     *
     * @param bool $headerAsField
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
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
}
