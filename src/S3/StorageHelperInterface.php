<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\S3;

/**
 * The StorageHelperInterface class.
 *
 * @since  {DEPLOY_VERSION}
 */
interface StorageHelperInterface
{
	/**
	 * getTempFile
	 *
	 * @return  string
	 */
	public static function getTempFile();

	/**
	 * getS3Path
	 *
	 * @return  string
	 */
	public static function getPath();

	/**
	 * getS3Url
	 *
	 * @return  string
	 */
	public static function getRemoteUrl();
}
