<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Command\Unidev;

use Windwalker\Core\Console\CoreCommand;
use Windwalker\Filesystem\File;
use Windwalker\Filesystem\Folder;
use Windwalker\Http\HttpClient;

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
     * @var  string
     */
    protected $file = 'https://raw.githubusercontent.com/lyrasoft/unidev/master/resources/ide/phpstorm/blade.xml';

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
        $dest = WINDWALKER_ROOT . '/.idea/blade.xml';

        if (!is_dir(dirname($dest))) {
            Folder::create(dirname($dest));
        }

        $remote = $this->getOption('r');
        $file   = $remote ? $this->file : realpath(__DIR__ . '/../../../resources/ide/phpstorm/blade.xml');

        $filePath = $this->getArgument(0) ?: $file;

        if ($remote) {
            $this->out('Downloading...');

            $http = new HttpClient;
            $http->download($filePath, $dest);

            $this->out('Downloaded <info>' . $filePath . '</info> to <info>.idea</info> folder');
        } else {
            File::copy($filePath, $dest, true);

            $this->out('Copy <info>' . $filePath . '</info> to <info>.idea</info> folder');
        }

        return true;
    }
}
