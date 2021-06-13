<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Helper;

use Windwalker\Legacy\Uri\Uri;

/**
 * The PravatarHelper class.
 *
 * @since  1.0
 */
class PravatarHelper
{
    /**
     * Property host.
     *
     * @var  string
     */
    protected static $host = 'https://i.pravatar.cc';

    /**
     * url
     *
     * @param int    $size
     * @param int    $id
     * @param string $u
     *
     * @return  string
     */
    public static function url($size = 300, $id = null, $u = null)
    {
        $uri = new Uri(static::$host);

        $size = $size ?: 300;

        $uri->setPath('/' . $size);

        if ($id) {
            $uri->setVar('id', (int) $id);
        }

        if ($u) {
            $uri->setVar('u', $u);
        }

        return $uri->toString();
    }

    /**
     * unique
     *
     * @param int    $size
     * @param string $u
     *
     * @return  string
     * @throws \Exception
     */
    public static function unique($size = 300, $u = null)
    {
        if ((string) $u === '') {
            $u = uniqid(random_int(1, 1000), true);
        }

        return static::url($size, null, $u);
    }
}
