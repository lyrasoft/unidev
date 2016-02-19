<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image;

use Lyrasoft\Unidev\Image\Storage\ImageStorageInterface;

/**
 * The ImageUploaderManager class.
 *
 * @since  {DEPLOY_VERSION}
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
	 * @param   string  $image
	 * @param   string  $path
	 *
	 * @return  string
	 */
	public function uploadRaw($image, $path)
	{
		return $this->adapter->uploadRaw($image, $path);
	}

	/**
	 * upload
	 *
	 * @param   string  $file
	 * @param   string  $path
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
	 * @param   string  $path
	 *
	 * @return  boolean
	 */
	public function delete($path)
	{
		return $this->adapter->delete($path);
	}
}
