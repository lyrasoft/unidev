<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image;

use Lyrasoft\Unidev\Image\Storage\ImageStorageInterface;

/**
 * The ImageUploaderManager class.
 *
 * @since  1.0
 */
class ImageUploaderManager
{
    /**
     * Property adapter.
     *
     * @var  ImageStorageInterface
     */
    protected $adapter;

    /**
     * ImageUploaderManager constructor.
     *
     * @param ImageStorageInterface $adapter
     */
    public function __construct(ImageStorageInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * uploadRaw
     *
     * @param   string $image
     * @param   string $path
     * @param   string $type
     *
     * @return string
     */
    public function uploadRaw($image, $path, $type = null)
    {
        return $this->adapter->uploadRaw($image, $path, $type);
    }

    /**
     * upload
     *
     * @param   string $file
     * @param   string $path
     *
     * @return  string
     */
    public function upload($file, $path)
    {
        return $this->adapter->upload($file, $path);
    }

    /**
     * delete
     *
     * @param   string $path
     *
     * @return  boolean
     */
    public function delete($path)
    {
        return $this->adapter->delete($path);
    }

    /**
     * Method to get property Adapter
     *
     * @return  ImageStorageInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Method to set property adapter
     *
     * @param   ImageStorageInterface $adapter
     *
     * @return  static  Return self to support chaining.
     */
    public function setAdapter(ImageStorageInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
