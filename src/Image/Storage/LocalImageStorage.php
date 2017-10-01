<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image\Storage;

use Lyrasoft\Unidev\S3\S3Helper;
use Windwalker\Core\Asset\Asset;
use Windwalker\Filesystem\File;
use Windwalker\Ioc;
use Windwalker\Structure\Structure;
use Windwalker\Utilities\Arr;

/**
 * The S3ImageStorage class.
 *
 * @since  1.0
 */
class LocalImageStorage implements ImageStorageInterface
{
	/**
	 * Property config.
	 *
	 * @var  Structure
	 */
	protected $config;

	/**
	 * Property path.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * ImgurImageStorage constructor.
	 *
	 * @param null      $nope
	 * @param Structure $config
	 */
	public function __construct($nope, Structure $config)
	{
		$this->config = $config;
		$this->path = $config->get('path');
	}

	/**
	 * uploadRaw
	 *
	 * @param  string $image
	 * @param  string $path
	 * @param  string $type
	 *
	 * @return string
	 * @throws \Windwalker\Filesystem\Exception\FilesystemException
	 */
	public function uploadRaw($image, $path, $type = null)
	{
		$file = $this->getFilepath($path);

		File::write($file, $image);

		return $this->getRemoteUrl($path);
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
		$dest = $this->getFilepath($path);

		if (is_file($dest))
		{
			File::delete($dest);
		}

		File::move($file, $dest);

		return $this->getRemoteUrl($path);
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
		return File::delete($this->getFilepath($path));
	}

	/**
	 * getFilepath
	 *
	 * @param string $path
	 *
	 * @return  string
	 */
	public function getFilepath($path)
	{
		return static::getPublicFolder() . '/' . ltrim($this->path, '/') . '/' . ltrim($path, '/');
	}

	/**
	 * getPublicFolder
	 *
	 * @return  string
	 */
	public static function getPublicFolder()
	{
		return WINDWALKER_PUBLIC;
	}

	/**
	 * getRemoteUrl
	 *
	 * @param   string  $uri
	 *
	 * @return  string
	 */
	public function getRemoteUrl($uri)
	{
		return $this->getHost() . '/' . ltrim($this->path, '/') . '/' . ltrim($uri, '/');
	}

	/**
	 * getHost
	 *
	 * @return  string
	 */
	public function getHost()
	{
		return Ioc::getUriData()->root;
	}

	/**
	 * Method to get property Path
	 *
	 * @return  string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Method to set property path
	 *
	 * @param   string $path
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}
}
