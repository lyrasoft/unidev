<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Provider;

use Lyrasoft\Unidev\Buffer\BufferFactory;
use Lyrasoft\Unidev\Image\ImageUploaderFactory;
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
		// S3
		$container->share(\S3::class, function(Container $container)
		{
			$config = $container->get('config');

			$endpoint = $config->get('unidev.amazon.endpoint', 's3.amazonaws.com');

			return new \S3($config->get('unidev.amazon.key'), $config->get('unidev.amazon.secret'), false, $endpoint);
		})->alias('unidev.storage.s3', \S3::class);

		// Imgur
		$container->prepareSharedObject(\Imgur\Client::class, function(\Imgur\Client $client, Container $container)
		{
			$config = $container->get('config');
			$client->setOption('client_id', $config->get('unidev.imgur.key'));
			$client->setOption('client_secret', $config->get('unidev.imgur.secret'));

			return $client;
		})->alias('unidev.storage.imgur', \Imgur\Client::class);

		// Ajax Response Buffer
		$closure = function(Container $container)
		{
			return new BufferFactory;
		};

		$container->share('unidev.buffer.factory', $closure);

		// Image Uploader
		$closure = function(Container $container)
		{
			return new ImageUploaderFactory($container);
		};

		$container->share('unidev.image.uploader.factory', $closure);

		// Uploader
		$closure = function(Container $container)
		{
			return $container->get('unidev.image.uploader.factory')->create();
		};

		$container->share('unidev.image.uploader', $closure);
	}
}
