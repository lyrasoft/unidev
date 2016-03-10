<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Storage;

/**
 * The AbstractStorageHelper class.
 *
 * @since  {DEPLOY_VERSION}
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

		if ($ext == 'jpeg')
		{
			$ext = 'jpg';
		}

		return $ext;
	}
}
