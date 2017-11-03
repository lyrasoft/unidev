<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Controller\Unidev;

use Gregwar\Image\Image;
use Lyrasoft\Unidev\Controller\AbstractAjaxController;
use Lyrasoft\Unidev\Image\ImageUploader;
use Lyrasoft\Unidev\Image\ImageUploadHelper;
use Phoenix\Controller\AbstractPhoenixController;
use Windwalker\Core\Controller\Traits\JsonApiTrait;
use Windwalker\Filesystem\File;
use Windwalker\Filesystem\Folder;
use Windwalker\Http\Helper\UploadedFileHelper;

/**
 * The ImageUploadController class.
 *
 * @since  1.0
 */
class ImageUploadController extends AbstractPhoenixController
{
	use JsonApiTrait;

	/**
	 * Property fieldName.
	 *
	 * @var  string
	 */
	protected $fieldName = 'file';

	/**
	 * Property resizeConfig.
	 *
	 * @var  array
	 */
	protected $resizeConfig;

	/**
	 * doAjax
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		if (!$this->app->get('unidev.image.storage'))
		{
			throw new \LogicException('No image storage set in config.');
		}

		$file = $this->input->files->get($this->fieldName);
		$folder = $this->input->getPath('folder');
		$folder = ltrim($folder . '/', '/');

		if (!$file || $file->getError())
		{
			$msg = 'Upload fail';

			if (WINDWALKER_DEBUG)
			{
				$msg .= ': ' . UploadedFileHelper::getUploadMessage($file->getError());
			}

			throw new \RuntimeException($msg, 500);
		}

		$id = $this->getImageName($file->getClientFilename());
		$temp = $this->getImageTemp($id, File::getExtension($file->getClientFilename()));

		if (!is_dir(dirname($temp)))
		{
			Folder::create(dirname($temp));
		}

		$file->moveTo($temp);

		$temp = $this->resize($temp);

		if (!is_file($temp))
		{
			throw new \RuntimeException('Temp file not exists');
		}

		$url = ImageUploader::upload($temp, $this->getImagePath($folder . $id, File::getExtension($temp)));

		File::delete($temp);

		$this->addMessage('Upload success.');

		return [
			'url' => $url
		];
	}

	/**
	 * getImageName
	 *
	 * @return  string
	 */
	protected function getImageName($name)
	{
		return md5(uniqid(mt_rand(0, 999)));
	}

	/**
	 * getImageTemp
	 *
	 * @param   string $file
	 *
	 * @return  string
	 */
	protected function getImageTemp($file, $ext = 'jpg')
	{
		return ImageUploadHelper::getTempFile($file, $ext);
	}

	/**
	 * getRemotePath
	 *
	 * @param string $name
	 * @param string $ext
	 *
	 * @return  string
	 */
	protected function getImagePath($name, $ext = 'jpg')
	{
		return ImageUploadHelper::getPath($name, $ext);
	}

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
	 * resize
	 *
	 * @link  https://github.com/Gregwar/Image
	 *
	 * @param   string  $file
	 *
	 * @return  string
	 */
	protected function resize($file)
	{
		if (!$this->app->get('unidev.image.resize.enabled', true))
		{
			return $file;
		}

		$app = $this->app;

		$resize = $app->config->extract('unidev.image.resize');

		$resize->load($this->resizeConfig);

		$width   = $resize->get('width', 1200);
		$height  = $resize->get('height', 1200);
		$quality = $resize->get('quality', 85);
		$crop    = $resize->get('crop', false);

		$image = Image::open($file);

		if ($image->width() < $width && $image->height() < $height)
		{
			return $file;
		}

		if ($crop)
		{
			$image->zoomCrop($width, $height);
		}
		else
		{
			$image->cropResize($width, $height);
		}

		$image->save($file, 'guess', $quality);

		return $file;
	}
}
