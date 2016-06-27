<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev;

use Lyrasoft\Unidev\Helper\UnidevHelper;
use Lyrasoft\Unidev\Listener\UnidevRoutingListener;
use Lyrasoft\Unidev\Provider\UnidevProvider;
use Windwalker\Core\Package\AbstractPackage;
use Windwalker\DI\Container;
use Windwalker\Event\Dispatcher;

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
