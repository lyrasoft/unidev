<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Storage;

/**
 * The StorageHelperInterface class.
 *
 * @since  1.0
 */
interface StorageHelperInterface
{
    /**
     * Get file temp path.
     *
     * @param   mixed $identify The identify of this file or item.
     *
     * @return  string  Identify path.
     */
    public static function getTempFile($identify);

    /**
     * Get remote uri path.
     *
     * @param   mixed $identify The identify of this file or item.
     *
     * @return  string  Identify path.
     */
    public static function getPath($identify);

    /**
     * Get remote url.
     *
     * @param   mixed $identify The identify of this file or item.
     *
     * @return  string  Identify URL.
     */
    public static function getRemoteUrl($identify);

    /**
     * Get base folder name.
     *
     * @return  string
     */
    public static function getBaseFolder();
}
