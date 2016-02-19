<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image;

use Windwalker\DI\Container;
use Windwalker\DI\ContainerAwareInterface;

/**
 * The ImageUploaderFactory class.
 *
 * @since  {DEPLOY_VERSION}
 */
class ImageUploaderFactory implements ContainerAwareInterface
{
	const STORAGE_S3 = 's3';
	const STORAGE_IMGUR = 'imgur';

	/**
	 * Property container.
	 *
	 * @var  Container
	 */
	protected $container;

	/**
	 * ImageUploaderFactory constructor.
	 *
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * create
	 *
	 * @param string $storage
	 *
	 * @return  ImageUploaderManager
	 */
	public function create($storage = null)
	{
		$storage = $storage ? : $this->container->get('system.config')->get('unidev.image.storage', static::STORAGE_S3);

		if (is_string($storage))
		{
			$class = __NAMESPACE__ . '\\Storage\\' . ucfirst($storage) . 'ImageStorage';

			$storage = new $class($this->container->get('unidev.storage.' . strtolower($storage)));
		}

		return new ImageUploaderManager($storage);
	}

	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Set the DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  mixed
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;

		return $this;
	}
}
