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
use Lyrasoft\Unidev\Image\Base64Image;
use Lyrasoft\Unidev\Image\ImageUploader;
use Lyrasoft\Unidev\Image\ImageUploadHelper;
use Phoenix\Controller\AbstractPhoenixController;
use Windwalker\Core\Controller\Traits\JsonApiTrait;
use Windwalker\Core\Language\Translator;
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
    protected $resizeConfig = [];

    /**
     * doAjax
     *
     * @return  mixed
     * @throws \Exception
     */
    protected function doExecute()
    {
        if (!$this->app->get('unidev.image.storage')) {
            throw new \LogicException('No image storage set in config.');
        }

        $format = $this->input->get('format', 'file');

        $folder = $this->input->getPath('folder');
        $resize = $this->input->getString('resize', $this->app->get('unidev.image.resize.enabled', true));
        $folder = ltrim($folder . '/', '/');

        switch ($format) {
            case 'base64':
                $file = $this->input->post->getString($this->fieldName);

                $id   = $this->getImageName(uniqid('Luna:image', true));
                $temp = $this->getImageTemp($id, Base64Image::getTypeFromBase64($file));

                if (!is_dir(dirname($temp))) {
                    Folder::create(dirname($temp));
                }

                Base64Image::toFile($file, $temp);
                break;

            case 'file':
            default:
                $file = $this->input->files->get($this->fieldName);

                if (!$file || $file->getError()) {
                    $msg = 'Upload fail';

                    if (WINDWALKER_DEBUG) {
                        $msg .= ': ' . UploadedFileHelper::getUploadMessage($file->getError());
                    }

                    throw new \RuntimeException($msg, 500);
                }

                $id   = $this->getImageName($file->getClientFilename());
                $temp = $this->getImageTemp($id, File::getExtension($file->getClientFilename()));

                if (!is_dir(dirname($temp))) {
                    Folder::create(dirname($temp));
                }

                $file->moveTo($temp);
                break;
        }

        if ($resize) {
            $resizeConfig = [];

            $size = $this->input->get('size');
            $width = $this->input->get('width');
            $height = $this->input->get('height');

            if ($size || $width || $height) {
                $resizeConfig['width'] = $width;
                $resizeConfig['height'] = $height;

                if ($size) {
                    [$width, $height] = array_pad(explode('x', strtolower($size)), 2, null);
                    $height = $height ?: $width;

                    // Override width x height
                    $resizeConfig['width'] = $width;
                    $resizeConfig['height'] = $height;
                }

                if ($crop = $this->input->get('crop', 0)) {
                    $resizeConfig['crop'] = $crop;
                }

                if ($quality = $this->input->getInt('quality', 85)) {
                    $resizeConfig['quality'] = $quality;
                }

                $temp = $this->resize($temp, $resizeConfig);
            }
        }

        if (!is_file($temp)) {
            throw new \RuntimeException('Temp file not exists');
        }

        $url = ImageUploader::upload($temp, $this->getImagePath($folder . $id, File::getExtension($temp)));

        File::delete($temp);

        $this->addMessage('Upload success.');

        return [
            'url' => $url,
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
     * @param string $file
     * @param string $ext
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
     * @param   string $ext
     *
     * @return  string
     */
    public static function getRealExtension($ext)
    {
        $ext = strtolower($ext);

        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        return $ext;
    }

    /**
     * resize
     *
     * @link  https://github.com/Gregwar/Image
     *
     * @param string $file
     * @param array  $config
     *
     * @return  string
     * @throws \Exception
     */
    protected function resize($file, array $config = [])
    {
        $app = $this->app;

        $resize = $app->config->extract('unidev.image.resize');

        $resize->load($config);

        $width   = $resize->get('width');
        $height  = $resize->get('height');
        $quality = $resize->get('quality', 85);
        $crop    = $resize->get('crop', false);

        $width = $width ?: null;
        $height = $height ?: null;

        try {
            $image = Image::open($file);

            if (($width && $image->width() < $width) && ($height && $image->height() < $height)) {
                return $file;
            }

            if ($crop) {
                $image->zoomCrop($width, $height);
            } elseif ($width || $height) {
                $image->cropResize($width, $height);
            }
        } catch (\UnexpectedValueException $e) {
            throw new \UnexpectedValueException(
                __('unidev.image.upload.message.load.fail'),
                $e->getCode(),
                $e
            );
        }

        $image->save($file, 'guess', $quality);

        return $file;
    }
}
