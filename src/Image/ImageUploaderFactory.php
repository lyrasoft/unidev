<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image;

use Windwalker\Legacy\DI\Container;
use Windwalker\Legacy\DI\ContainerAwareInterface;

/**
 * The ImageUploaderFactory class.
 *
 * @since  1.0
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
        $config = $this->container->get('config');

        $storage = $storage ?: $config->get('unidev.image.storage');

        if (!$storage) {
            throw new \DomainException('No image storage provider.');
        }

        if (is_string($storage)) {
            $class = __NAMESPACE__ . '\\Storage\\' . ucfirst($storage) . 'ImageStorage';

            $key    = 'unidev.storage.' . strtolower($storage);
            $client = null;

            if ($this->container->exists($key)) {
                $client = $this->container->get($key);
            }

            $storage = $this->container->createSharedObject($class, [
                'config' => $config->extract('unidev.' . $storage)
            ]);
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
