<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image\Storage;

use Lyrasoft\Unidev\S3\S3Helper;
use Windwalker\Filesystem\File;
use Windwalker\Utilities\Arr;

/**
 * The S3ImageStorage class.
 *
 * @since  1.0
 */
class S3ImageStorage implements ImageStorageInterface
{
    /**
     * uploadRaw
     *
     * @param   string $image
     * @param   string $path
     * @param   string $type
     *
     * @return string
     */
    public function uploadRaw($image, $path, $type = null)
    {
        $path = ltrim(S3Helper::getSubfolder() . '/' . $path, '/');

        if (!$type) {
            $types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ];

            $type = Arr::get($types, strtolower(File::getExtension($path)));
        }

        $file = [
            'data' => $image,
            'size' => strlen($image),
            'type' => $type,
        ];

        S3Helper::putObject($file, S3Helper::getBucketName(), $path, \S3::ACL_PUBLIC_READ);

        return $this->getRemoteUrl($path);
    }

    /**
     * upload
     *
     * @param   string $file
     * @param   string $path
     *
     * @return  string
     */
    public function upload($file, $path)
    {
        S3Helper::upload($file, $path);

        return $this->getRemoteUrl($path);
    }

    /**
     * delete
     *
     * @param   string $path
     *
     * @return  boolean
     */
    public function delete($path)
    {
        return S3Helper::delete($path);
    }

    /**
     * getHost
     *
     * @return  string
     */
    public function getHost()
    {
        return S3Helper::getHost();
    }

    /**
     * getRemoteUrl
     *
     * @param   string $uri
     *
     * @return  string
     */
    public function getRemoteUrl($uri)
    {
        return rtrim($this->getHost(), '/') . '/' . ltrim($uri, '/');
    }
}
