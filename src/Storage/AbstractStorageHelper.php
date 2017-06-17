<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Storage;

use Lyrasoft\Unidev\Image\ImageUploader;

/**
 * The AbstractStorageHelper class.
 *
 * @since  1.0
 */
abstract class AbstractStorageHelper implements StorageHelperInterface
{
	/**
	 * getRealExtension
	 *
	 * @param   string  $ext
	 *
	 * @return  string
	 */
	public static function getRealExtension($ext)
	{
		$ext = strtolower($ext);

		if ($ext === 'jpeg')
		{
			$ext = 'jpg';
		}

		return $ext;
	}

	/**
	 * Get file temp path.
	 *
	 * @param   mixed $identify The identify of this file or item.
	 *
	 * @return  string  Identify path.
	 */
	public static function getTempFile($identify)
	{
		return static::getTempPath() . '/' . static::getPath($identify);
	}

	/**
	 * getTempPath
	 *
	 * @return  string
	 */
	public static function getTempPath()
	{
		return WINDWALKER_TEMP . '/lyra';
	}

	/**
	 * Get remote url.
	 *
	 * @param   mixed $identify The identify of this file or item.
	 *
	 * @return  string  Identify URL.
	 */
	public static function getRemoteUrl($identify)
	{
		return ImageUploader::getAdapter()->getHost() . '/' . static::getPath($identify);
	}
}
