<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image\Storage;

use Lyrasoft\Unidev\Image\Base64Image;
use Windwalker\Filesystem\File;

/**
 * The ImgurImageStorage class.
 *
 * @since  {DEPLOY_VERSION}
 */
class ImgurImageStorage implements ImageStorageInterface
{
	/**
	 * Property imgur.
	 *
	 * @var  \Imgur
	 */
	protected $imgur;

	/**
	 * ImgurImageStorage constructor.
	 *
	 * @param \Imgur $imgur
	 */
	public function __construct(\Imgur $imgur)
	{
		$this->imgur = $imgur;
	}

	/**
	 * uploadRaw
	 *
	 * @param   string $image
	 * @param   string $path
	 *
	 * @return  string
	 */
	public function uploadRaw($image, $path)
	{
		$ext = File::getExtension($path);

		return $this->imgur->upload()->string(Base64Image::encode($image, $ext));
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
		return $this->imgur->upload()->file($file);
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

	}
}
