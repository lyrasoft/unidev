<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image;

use Lyrasoft\Unidev\Image\Storage\ImageStorageInterface;
use Windwalker\Core\Facade\AbstractProxyFacade;

/**
 * The ImageUploader class.
 *
 * @see    ImageUploaderManager
 *
 * @method  static string                 uploadRaw($image, $path, $type = null)
 * @method  static string                 upload($file, $path)
 * @method  static string                 delete($path)
 * @method  static ImageStorageInterface  getAdapter()
 * @method  static void                   setAdapter(ImageStorageInterface $adapter)
 *
 * @since  1.0
 */
class ImageUploader extends AbstractProxyFacade
{
    /**
     * Property _key.
     *
     * @var  string
     */
    protected static $_key = 'unidev.image.uploader';
}
