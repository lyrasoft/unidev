<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Helper;

use Lyrasoft\Unidev\UnidevPackage;

/**
 * The UnidevHelper class.
 *
 * @since  1.0
 */
class UnidevHelper
{
    /**
     * Property package.
     *
     * @var  UnidevPackage
     */
    protected static $package;

    /**
     * setPackage
     *
     * @param   UnidevPackage $unidev
     *
     * @return  void
     */
    public static function setPackage(UnidevPackage $unidev)
    {
        static::$package = $unidev;
    }

    /**
     * Method to get property Package
     *
     * @return  UnidevPackage
     */
    public static function getPackage()
    {
        return static::$package;
    }
}
