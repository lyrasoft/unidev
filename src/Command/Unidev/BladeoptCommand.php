<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Command\Unidev;

use Windwalker\Core\Console\CoreCommand;
use Windwalker\Http\HttpClient;

/**
 * The BladeoptCommand class.
 *
 * @since  {DEPLOY_VERSION}
 */
class BladeoptCommand extends CoreCommand
{
	protected $name = 'bladeopt';

	protected $description = 'Install blade optimization for phpstorm';

	protected $file = 'https://raw.githubusercontent.com/lyrasoft/development-tools/master/Editor/PHPStorm/blade.xml';

	protected function doExecute()
	{
		$dest = WINDWALKER_ROOT . '/.idea/blade.xml';
		$http = new HttpClient;
		$http->download($this->file, $dest);

		$this->out('Downloaded <info>blade.xml</info> to <info>.idea</info> folder');

		return true;
	}
}
