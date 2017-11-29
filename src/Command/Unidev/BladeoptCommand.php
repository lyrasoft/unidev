<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Command\Unidev;

use Windwalker\Core\Console\CoreCommand;
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
	 * doExecute
	 *
	 * @return  bool
	 */
	protected function doExecute()
	{
		$dest = WINDWALKER_ROOT . '/.idea/blade.xml';

		if (!is_dir(dirname($dest)))
		{
			Folder::create(dirname($dest));
		}

		$file = $this->getArgument(0) ? : $this->file;

		$http = new HttpClient;
		$http->download($file, $dest);

		$this->out('Downloaded <info>' . $file . '</info> to <info>.idea</info> folder');

		return true;
	}
}
