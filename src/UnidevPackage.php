<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev;

use Lyrasoft\Unidev\Helper\UnidevHelper;
use Lyrasoft\Unidev\Provider\UnidevProvider;
use Windwalker\Core\Package\AbstractPackage;
use Windwalker\DI\Container;

define('UNIDEV_PACKAGE_ROOT', __DIR__);

/**
 * The UnidevPackage class.
 *
 * @since  {DEPLOY_VERSION}
 */
class UnidevPackage extends AbstractPackage
{
	/**
	 * UnidevPackage constructor.
	 */
	public function __construct()
	{
		UnidevHelper::setPackage($this);
	}

	/**
	 * registerProviders
	 *
	 * @param Container $container
	 *
	 * @return  void
	 */
	public function registerProviders(Container $container)
	{
		$container->getParent()->registerServiceProvider(new UnidevProvider);
	}
}
