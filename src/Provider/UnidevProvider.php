<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Provider;

use Windwalker\DI\Container;
use Windwalker\DI\ServiceProviderInterface;

/**
 * The UnidevProvider class.
 *
 * @since  {DEPLOY_VERSION}
 */
class UnidevProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  void
	 */
	public function register(Container $container)
	{
		$closure = function(Container $container)
		{
			$config = $container->get('system.config');

			$endpoint = $config->get('amazon.endpoint', 's3.amazonaws.com');

			return new \S3($config->get('amazon.key'), $config->get('amazon.secret'), false, $endpoint);
		};

		$container->share('unidev.s3', $closure);
	}
}
