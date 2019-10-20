<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Image\Storage;

/**
 * The AbstractStorage class.
 *
 * @since  1.0
 */
interface ImageStorageInterface
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
    public function uploadRaw($image, $path, $type = null);

    /**
     * upload
     *
     * @param   string $file
     * @param   string $path
     *
     * @return  string
     */
    public function upload($file, $path);

    /**
     * delete
     *
     * @param   string $path
     *
     * @return  boolean
     */
    public function delete($path);

    /**
     * getHost
     *
     * @return  string
     */
    public function getHost();

    /**
     * getRemoteUrl
     *
     * @param   string $uri
     *
     * @return  string
     */
    public function getRemoteUrl($uri);
}
