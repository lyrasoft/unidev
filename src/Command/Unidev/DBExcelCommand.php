<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2020 .
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Command\Unidev;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Windwalker\Console\Exception\WrongArgumentException;
use Windwalker\Core\Console\CoreCommand;
use Windwalker\Filesystem\File;

/**
 * The DBExcelCommand class.
 *
 * @since  1.5.22
 */
class DBExcelCommand extends CoreCommand
{
    /**
     * Property name.
     *
     * @var  string
     */
    protected $name = 'db-excel';

    /**
     * Property description.
     *
     * @var  string
     */
    protected $description = 'Export DB schema to Excel file';

    /**
     * The usage to tell user how to use this command.
     *
     * @var string
     */
    protected $usage = '%s <cmd><export_path></cmd> <option>[option]</option>';

    /**
     * The manual about this command.
     *
     * @var  string
     */
    protected $help;

    /**
     * Initialise command.
     *
     * @return void
     */
    protected function init()
    {
        parent::init();
    }

    /**
     * Execute this command.
     *
     * @return int|bool
     */
    protected function doExecute()
    {
        $dest = $this->getArgument(0);

        if (!$dest) {
            throw new WrongArgumentException('Please provide dest path.');
        }

        if (strpos($dest, '/') !== 0) {
            $dest = getcwd() . '/' . $dest;
        }

        $excel = new Spreadsheet();
        $db = $this->console->database;

        foreach ($db->getDatabase()->getTables() as $table) {
            $table = $db->getTable($table);

            $columns = array_values($table->getColumnDetails());

            $cols = [
                [
                    '#',
                    'Name',
                    'Type',
                    'Nullable',
                    'Default',
                    'Extra',
                    'Comment'
                ]
            ];

            foreach ($columns as $i => $column) {
                $column = (array) $column;

                $cols[] = [
                    $i + 1,
                    $column['Field'],
                    $column['Type'],
                    $column['Null'],
                    $column['Default'],
                    $column['Extra'],
                    $column['Comment'],
                ];
            }

            $sheet = $excel->createSheet();
            $sheet->setTitle($table->getName());
            $sheet->fromArray($cols, null, 'A1');
        }

        $format = File::getExtension($dest);

        $writer = IOFactory::createWriter($excel, ucfirst($format));

        $writer->save($dest);

        $this->out('Save DB Schema to: ' . $dest);

        return true;
    }
}
