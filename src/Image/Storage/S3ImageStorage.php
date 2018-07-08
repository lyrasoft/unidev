<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image\Storage;

use Lyrasoft\Unidev\S3\S3Service;
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
     * Property s3.
     *
     * @var  S3Service
     */
    protected $s3;

    /**
     * S3ImageStorage constructor.
     *
     * @param S3Service $s3
     */
    public function __construct(S3Service $s3)
    {
        $this->s3 = $s3;
    }


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
        $path = ltrim($this->s3->getSubfolder() . '/' . $path, '/');

        if (!$type) {
            $types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ];

            $type = Arr::get($types, strtolower(File::getExtension($path)));
        }

        return $this->s3->uploadFileData($image, $path, [
            'ACL' => S3Service::ACL_PUBLIC_READ,
            'ContentType' => $type,
            'ContentLength' => strlen($image)
        ])->get('ObjectURL');
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
        return $this->s3->uploadFile($file, $path, [
            'ACL' => S3Service::ACL_PUBLIC_READ
        ])->get('ObjectURL');
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
        $this->s3->deleteObject($path);

        return true;
    }

    /**
     * getHost
     *
     * @return  string
     */
    public function getHost()
    {
        return $this->s3->getHost();
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
