<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Helper;

use Lyrasoft\Unidev\UnidevPackage;

/**
 * The UnidevHelper class.
 *
 * @since  {DEPLOY_VERSION}
 */
class UnidevHelper
{
	/**
	 * Property package.
	 *
	 * @var  UnidevPackage
	 */
	protected static $package;

	/**
	 * setPackage
	 *
	 * @param   UnidevPackage $luna
	 *
	 * @return  void
	 */
	public static function setPackage(UnidevPackage $luna)
	{
		static::$package = $luna;
	}

	/**
	 * Method to get property Package
	 *
	 * @return  UnidevPackage
	 */
	public static function getPackage()
	{
		return static::$package;
	}
}
