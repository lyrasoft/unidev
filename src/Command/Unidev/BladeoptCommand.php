<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Command\Unidev;

use Windwalker\Legacy\Core\Console\CoreCommand;
use Windwalker\Legacy\Filesystem\File;
use Windwalker\Legacy\Filesystem\Filesystem;
use Windwalker\Legacy\Filesystem\Folder;
use Windwalker\Legacy\Http\HttpClient;

/**
 * The BladeoptCommand class.
 *
 * @since  1.0
 */
class BladeoptCommand extends CoreCommand
{
    /**
     * Property name.
     *
     * @var  string
     */
    protected $name = 'bladeopt';

    /**
     * Property description.
     *
     * @var  string
     */
    protected $description = 'Install blade optimization for phpstorm';

    /**
     * Property file.
     *
     * @var  string[]
     */
    protected $files = [
        'https://raw.githubusercontent.com/lyrasoft/unidev/master/resources/ide/phpstorm/blade.xml',
        'https://raw.githubusercontent.com/lyrasoft/unidev/master/resources/ide/phpstorm/laravel-plugin.xml',
    ];

    /**
     * Initialise command.
     *
     * @return void
     *
     * @since  2.0
     */
    protected function init()
    {
        $this->addOption('r')
            ->alias('remote')
            ->description('Download blade.xml from remote repository.')
            ->defaultValue(false);
    }

    /**
     * doExecute
     *
     * @return  bool
     */
    protected function doExecute()
    {
        $dest = WINDWALKER_ROOT . '/.idea';

        if (!is_dir($dest)) {
            Folder::create($dest);
        }

        $remote = $this->getOption('r');

        if ($remote) {
            $this->out('Downloading...');

            $http = new HttpClient();

            foreach ($this->files as $file) {
                $http->download($file, $dest . '/' . File::basename($file));
                $this->out('Downloaded <info>' . $file . '</info> to <info>.idea</info> folder');
            }
        } else {
            $folder = realpath(__DIR__ . '/../../../resources/ide/phpstorm');

            /** @var \SplFileInfo $file */
            foreach (Filesystem::files($folder) as $file) {
                File::copy($folder . '/' . $file->getBasename(), $dest . '/' . $file->getBasename(), true);
                $this->out('Copy <info>' . $folder . '/' . $file->getBasename() . '</info> to <info>.idea</info> folder');
            }
        }

        return true;
    }
}
