<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image;

use Windwalker\Filesystem\File;
use Windwalker\Filesystem\Folder;

/**
 * The Base64Image class.
 *
 * @since  {DEPLOY_VERSION}
 */
class Base64Image
{
	const TYPE_JPEG = 'jpeg';
	const TYPE_PNG = 'png';

	/**
	 * Property fileTypes.
	 *
	 * @var  array
	 */
	protected static $fileTypes = array(
		self::TYPE_JPEG => 'jpg',
		self::TYPE_PNG => 'png'
	);

	/**
	 * decode
	 *
	 * @param   string  $base64
	 *
	 * @return  string
	 */
	public static function decode($base64)
	{
		preg_match('/data:(\w+\/\w+);base64,(.*)/', $base64, $matches);

		$code = $matches[2];

		return base64_decode($code);
	}

	/**
	 * encode
	 *
	 * @param string $image
	 * @param string $type
	 *
	 * @return  string
	 */
	public static function encode($image, $type = self::TYPE_JPEG)
	{
		return 'data:image/' . $type . ';base64,' . base64_encode($image);
	}

	/**
	 * toFile
	 *
	 * @param   string  $base64
	 * @param   string  $file
	 *
	 * @return  boolean
	 */
	public static function toFile($base64, $file)
	{
		$image = static::decode($base64);

		if (!is_dir(dirname($file)))
		{
			Folder::create(dirname($file));
		}

		file_put_contents($file, $image);

		return true;
	}

	/**
	 * loadFile
	 *
	 * @param string $file
	 * @param string $type
	 *
	 * @return  string
	 */
	public static function loadFile($file, $type = null)
	{
		if (!is_file($file))
		{
			throw new \RuntimeException('File not found.');
		}

		$image = file_get_contents($file);

		$type = $type ? : File::getExtension($file);
		$type = $type == 'jpg' ? 'jpeg' : $type;

		return static::encode($image, $type);
	}

	/**
	 * getType
	 *
	 * @param   string  $base64
	 *
	 * @return  string
	 */
	public static function getTypeFromBase64($base64)
	{
		preg_match('/data:image\/(\w+);/', $base64, $matches);

		if ($matches[1])
		{
			$type = $matches[1];

			return static::getFileType($type);
		}

		return null;
	}

	/**
	 * getFileType
	 *
	 * @param   string  $type
	 *
	 * @return  string
	 */
	public static function getFileType($type)
	{
		if (isset(static::$fileTypes[$type]))
		{
			return static::$fileTypes[$type];
		}

		return null;
	}

	/**
	 * quickUpload
	 *
	 * @param   string  $base64
	 * @param   string  $uri
	 *
	 * @return  string
	 */
	public static function quickUpload($base64, $uri)
	{
		$ext = Base64Image::getTypeFromBase64($base64);

		if (!$ext)
		{
			return false;
		}

		$temp = WINDWALKER_TEMP . '/luna/images/temp/' . gmdate('Ymd') . '/' . md5(uniqid(rand(1, 999))) . '.' . $ext;

		if (!is_dir(dirname($temp)))
		{
			Folder::create(dirname($temp));
		}

		Base64Image::toFile($base64, $temp);

		// Upload to Cloud
		$url = ImageUploader::upload($temp, $uri);

		if (is_file($temp))
		{
			File::delete($temp);
		}

		return $url;
	}
}
