<?php
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Helper;

use Windwalker\Filesystem\File;
use Windwalker\Http\HttpClient;
use Windwalker\Utilities\ArrayHelper;

/**
 * The UnsplashHelper class.
 *
 * @since  1.0
 */
class UnsplashHelper
{
	/**
	 * Property images.
	 *
	 * @var  array
	 */
	protected static $images = [];

	/**
	 * Property ids.
	 *
	 * @var  array
	 */
	protected static $ids = null;

	/**
	 * init
	 *
	 * @return  array
	 */
	protected function init()
	{
		if (static::$ids === null)
		{
			$file = static::getTempPath();

			if (!is_file($file))
			{
				static::dump();
			}

			$content = file_get_contents($file);

			static::$ids = (array) explode(',', $content);
		}

		return static::$ids;
	}

	/**
	 * getTempPath
	 *
	 * @return  string
	 */
	protected function getTempPath()
	{
		return WINDWALKER_TEMP . '/unidev/images/unsplash-list.data';
	}

	/**
	 * getImageUrl
	 *
	 * @param int  $width
	 * @param int  $height
	 * @param int  $id
	 *
	 * @return  string
	 */
	public static function getImageUrl($width = 800, $height = 600, $id = null)
	{
		static::init();

		if (!isset(static::$ids[$id]))
		{
			$id = static::$ids[array_rand(static::$ids)];
		}

		return 'https://unsplash.it/' . $width . '/' . $height . '?image=' . $id;
	}

	/**
	 * getList
	 *
	 * @return  array
	 */
	public static function getList()
	{
		if (!static::$images)
		{
			$http = new HttpClient;
			$response = $http->get('https://unsplash.it/list');

			$images = json_decode($response->getBody()->__toString());

			foreach ($images as $image)
			{
				static::$images[$image->id] = $image;
			}
		}

		return static::$images;
	}

	/**
	 * dump
	 *
	 * @return  void
	 */
	public static function dump()
	{
		$list = static::getList();
		
		$ids = ArrayHelper::getColumn($list, 'id');
		
		$content = implode(',', $ids);
		
		File::write(static::getTempPath(), $content);
	}
}
