<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev;

use Lyrasoft\Unidev\Listener\UnidevListener;
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

	/**
	 * registerListeners
	 *
	 * @param Dispatcher $dispatcher
	 *
	 * @return  void
	 */
	public function registerListeners(Dispatcher $dispatcher)
	{
		parent::registerListeners($dispatcher);

		$dispatcher->addListener(new UnidevListener($this));
	}
}
