<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image\Storage;

use Lyrasoft\Unidev\S3\S3Helper;

/**
 * The S3ImageStorage class.
 *
 * @since  {DEPLOY_VERSION}
 */
class S3ImageStorage implements ImageStorageInterface
{
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
		S3Helper::putObject($image, S3Helper::getBucketName(), $path, \S3::ACL_PUBLIC_READ);

		return S3Helper::getHost() . '/' . $path;
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
		S3Helper::upload($file, $path);

		return S3Helper::getHost() . '/' . $path;
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
		return S3Helper::delete($path);
	}
}
