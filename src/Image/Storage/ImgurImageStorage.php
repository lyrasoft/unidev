<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image\Storage;

use Imgur\Client;
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
	 * @var  Client
	 */
	protected $imgur;

	/**
	 * ImgurImageStorage constructor.
	 *
	 * @param Client $imgur
	 */
	public function __construct(Client $imgur)
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
		$data = array(
			'image' => base64_encode($image),
			'type' => 'base64'
		);

		$basic = $this->imgur->api('image')->upload($data)->getData();

		return $basic['link'];
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
		$data = array(
			'image' => $file,
			'type' => 'file'
		);

		$basic = $this->imgur->api('image')->upload($data)->getData();

		return $basic['link'];
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
		$path = File::stripExtension($path);

		$basic = $this->imgur->api('image')->deleteImage($path)->getData();

		return $basic['success'];
	}
}
