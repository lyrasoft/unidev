<?php
/**
 * Part of ke project.
 *
 * @copyright  Copyright (C) 2019 .
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Excel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Debugger\Helper\DebuggerHelper;
use Windwalker\Filesystem\File;
use Windwalker\Filesystem\Folder;
use Windwalker\Http\Helper\HeaderHelper;
use Windwalker\Http\Output\Output;
use Windwalker\Http\Response\Response;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The ExcelExporter class.
 *
 * OPTIONS:
 *   - title: string
 *   - creator: string
 *   - description: string
 *   - show_header: bool
 *
 * USAGE:
 * ```
 * $exporter = new ExcelExporter(['title' => 'foo.xlsx']);
 *
 * $exporter->addColumn('id', 'ID', [options]);
 * $exporter->addColumn('title', 'Title', [options]);
 *
 * foreach ($items as $item) {
 *     $exporter->addRow(function (ExcelExporter $row) {
 *         $row->setRowCell('id', ...);
 *         $row->setRowCell('title', ...);
 *     });
 * }
 *
 * $exporter->download(); // Or render(): string
 * ```
 *
 * @since  1.5.13
 */
class ExcelExporter
{
    use OptionAccessTrait;

    /**
     * Property options.
     *
     * @var  array
     */
    protected $options = [];

    /**
     * Property columns.
     *
     * @var  array
     */
    protected $columns = [];

    /**
     * Property data.
     *
     * @var  array
     */
    protected $data = [];

    /**
     * Property row.
     *
     * @var  int
     */
    protected $currentRow;

    /**
     * ExcelExporter constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!class_exists(Spreadsheet::class)) {
            throw new \DomainException('Please install phpoffice/phpspreadsheet first.');
        }

        $this->options = $options;
    }

    /**
     * addColumn
     *
     * Options:
     *   - width
     *   - number_format: https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes/#styles
     *   - handler: callable($dimension, $code)
     *
     * @param string $id
     * @param string $title
     * @param array  $options
     *
     * @return  static
     *
     * @since  1.5.13
     */
    public function addColumn(string $id, string $title = '', array $options = []): self
    {
        $options['title'] = $title;

        $this->columns[$id] = $options;

        return $this;
    }

    /**
     * deleteRow
     *
     * @param string $id
     *
     * @return  static
     *
     * @since  1.5.13
     */
    public function deleteColumn(string $id): self
    {
        unset($this->columns[$id]);

        return $this;
    }

    /**
     * Method to get property Columns
     *
     * @return  array
     *
     * @since  1.5.13
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Method to set property columns
     *
     * @param array $columns
     *
     * @return  static  Return self to support chaining.
     *
     * @since  1.5.13
     */
    public function setColumns(array $columns): self
    {
        $this->columns = [];

        foreach ($columns as $name => $column) {
            if (is_array($column)) {
                $title = $column['title'] ?? '';
                $options = $column;
            } else {
                $title = $column;
                $options = [];
            }

            $this->addColumn($name, $title, $options);
        }

        return $this;
    }

    /**
     * addRow
     *
     * @param callable|null $handler
     *
     * @return  static
     *
     * @since  1.5.13
     */
    public function addRow(?callable $handler = null): self
    {
        $this->data[] = [];

        $this->currentRow = array_key_last($this->data);

        if ($handler) {
            $handler($this);
        }

        return $this;
    }

    /**
     * getRow
     *
     * @param int $rowId
     *
     * @return  array|null
     *
     * @since  1.5.13
     */
    public function getRow(int $rowId): ?array
    {
        return $this->data[$rowId] ?? null;
    }

    /**
     * deleteRow
     *
     * @param int $rowId
     *
     * @return  ExcelExporter
     *
     * @since  1.5.13
     */
    public function deleteRow(int $rowId): self
    {
        unset($this->data[$rowId]);

        if ($rowId === $this->currentRow) {
            $this->currentRow = array_key_last($this->data);
        }

        return $this;
    }

    /**
     * Method to get property CurrentRow
     *
     * @return  int
     *
     * @since  1.5.13
     */
    public function getCurrentRow(): int
    {
        return $this->currentRow;
    }

    /**
     * Method to set property currentRow
     *
     * @param int $currentRow
     *
     * @return  static  Return self to support chaining.
     *
     * @since  1.5.13
     */
    public function setCurrentRow(int $currentRow): self
    {
        $this->currentRow = $currentRow;

        return $this;
    }

    /**
     * setRowData
     *
     * @param array    $data
     * @param int|null $rowId
     *
     * @return  ExcelExporter
     *
     * @since  1.5.13
     */
    public function setRowData(array $data, ?int $rowId = null): self
    {
        $rowId = $rowId ?? $this->currentRow;

        $this->data[$rowId] = $data;

        return $this;
    }

    /**
     * setRowCell
     *
     * @param string   $name
     * @param mixed    $value
     * @param int|null $rowId
     *
     * @return  ExcelExporter
     *
     * @since  1.5.13
     */
    public function setRowCell(string $name, $value, ?int $rowId = null): self
    {
        $rowId = $rowId ?? $this->currentRow;
        $rowId = $rowId ?: 0;

        $this->data[$rowId][$name] = $value;

        return $this;
    }

    /**
     * Method to get property Data
     *
     * @return  array
     *
     * @since  1.5.13
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Method to set property data
     *
     * @param array $data
     *
     * @return  static  Return self to support chaining.
     *
     * @since  1.5.13
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * render
     *
     * @param array $options
     * @param string $format
     *
     * @return  string
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @since  1.5.13
     */
    public function render(array $options = [], string $format = 'xlsx'): string
    {
        $file = WINDWALKER_TEMP . '/unidev/excel/' . md5(uniqid('Unidev', true)) . '.' . $format;

        $this->save($file, $options, $format);

        $content = file_get_contents($file);

        File::delete($file);

        return $content;
    }

    /**
     * save
     *
     * @param string $file
     * @param array  $options
     * @param string $format
     *
     * @return  void
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @since  1.5.13
     */
    public function save(string $file, array $options = [], string $format = 'xlsx'): void
    {
        if (strpos($file, 'php://') === false) {
            Folder::create(dirname($file));
        }

        $this->prepareExcelWriter($options, $format)->save($file);
    }

    /**
     * download
     *
     * @param string $filename
     * @param array  $options
     * @param string $format
     *
     * @return  void
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Exception
     * @since  1.5.13
     */
    public function download(?string $filename = null, array $options = [], string $format = 'xlsx'): void
    {
        DebuggerHelper::disableConsole();

        if (!$filename && $this->getOption('title')) {
            $filename = $this->getOption('title') . '.' . $format;
        }

        if (!$filename) {
            $filename = 'Export-' . Chronos::toLocalTime('now', 'Y-m-d-H-i-s') . '.' . $format;
        }

        // Redirect output to a client’s web browser (Xlsx)
        $response = HeaderHelper::prepareAttachmentHeaders(new Response(), $filename);

        (new Output())->sendHeaders($response);

        $this->save('php://output', $options, $format);
        die;
    }

    /**
     * prepareExcelWriter
     *
     * @param array  $options
     * @param string $format
     *
     * @return  IWriter
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @since  1.5.13
     */
    protected function prepareExcelWriter(array $options = [], string $format = 'xlsx'): IWriter
    {
        $spreadsheet = $this->getSpreadsheet($options);

        return IOFactory::createWriter($spreadsheet, ucfirst($format));
    }

    /**
     * prepareSpreadsheet
     *
     * @param Spreadsheet|null $spreadsheet
     *
     * @return  Spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @since  1.5.18
     */
    public function getSpreadsheet(array $options = [], ?Spreadsheet $spreadsheet = null): Spreadsheet
    {
        $spreadsheet = $spreadsheet ?? new Spreadsheet();

        $creator = (string) $this->getOption('creator');
        $title   = (string) $this->getOption('title');
        $desc    = (string) $this->getOption('description');

        $properties = $spreadsheet->getProperties();

        if ($creator !== '') {
            $properties->setCreator($this->getOption('creator', 'Windwalker'));
        }

        if ($title !== '') {
            $properties->setTitle($title)
                ->setSubject($title);
        }

        if ($desc !== '') {
            $properties->setDescription($desc);
        }

        $preprocess = $options['preprocess'] ?? null;

        if ($preprocess) {
            $preprocess($spreadsheet);
        }

        $sheet = $spreadsheet->getActiveSheet();

        $dataset = [];

        if (Arr::get($options, 'show_header', true)) {
            $dataset[] = array_column($this->columns, 'title');
        }

        foreach ($this->data as $datum) {
            $row = [];

            foreach ($this->columns as $id => $column) {
                $row[] = $datum[$id] ?? '';
            }

            $dataset[] = $row;
        }

        foreach (array_values($this->columns) as $i => $column) {
            $code = static::num2alpha($i);

            $style = $sheet->getStyle($code . ':' . $code);
            $dimension = $sheet->getColumnDimension($code);

            if ($column['width'] ?? null) {
                $dimension->setWidth($column['width']);
            }

            if ($column['number_format'] ?? null) {
                $style->getNumberFormat()->setFormatCode($column['number_format']);
            }

            if (is_callable($column['handler'] ?? null)) {
                $column['handler']($dimension, $code);
            }
        }

        $sheet->fromArray($dataset);

        $postprocess = $options['postprocess'] ?? null;

        if ($postprocess) {
            $postprocess($spreadsheet);
        }

        return $spreadsheet;
    }

    /**
     * num2alpha
     *
     * @see https://stackoverflow.com/a/5554413
     *
     * @param int $n
     *
     * @return  string
     *
     * @since  1.5.13
     */
    public static function num2alpha(int $n): string
    {
        for ($r = ''; $n >= 0; $n = (int) ($n / 26) - 1) {
            $r = chr($n % 26 + 0x41) . $r;
        }

        return $r;
    }

    /**
     * alpha2num
     *
     * @see https://stackoverflow.com/a/5554413
     *
     * @param int $a
     *
     * @return  string
     *
     * @since  1.5.13
     */
    public static function alpha2num(int $a): string
    {
        $l = strlen($a);
        $n = 0;

        for ($i = 0; $i < $l; $i++) {
            $n = $n * 26 + ord($a[$i]) - 0x40;
        }

        return $n - 1;
    }
}
