<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2017 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Image;

use Lyrasoft\Unidev\UnidevPackage;
use Windwalker\Legacy\Core\Asset\Asset;
use Windwalker\Legacy\Core\Package\PackageHelper;
use Windwalker\Legacy\Dom\HtmlElement;
use Windwalker\Legacy\Utilities\Arr;

/**
 * The ImageHtmlHelper class.
 *
 * @since  1.3
 */
class ImageHtmlHelper
{
    /**
     * bgThumb
     *
     * @param string     $image
     * @param int|string $width
     * @param int|string $height
     * @param string     $sizeType
     * @param array      $attribs
     *
     * @return string
     */
    public static function bgThumb($image = null, $width = 50, $height = 50, $sizeType = 'cover', $attribs = [])
    {
        if (!$image) {
            $image = static::defaultImage();
        }

        $width  = is_numeric($width) ? $width . 'px' : $width;
        $height = is_numeric($height) ? $height . 'px' : $height;

        $image = static::addBase(htmlentities($image, ENT_QUOTES, 'UTF-8'));

        $style            = "width: $width; height: $height; background-image: url('$image'); background-size: $sizeType; background-position: center center; background-repeat: no-repeat;";
        $attribs['style'] = Arr::get($attribs, 'style') . ';' . $style;
        $attribs['class'] = Arr::get($attribs, 'class') . ' sq-thumb';

        return new HtmlElement('div', '', $attribs);
    }

    /**
     * defaultImage
     *
     * @return  string
     */
    public static function defaultImage()
    {
        $alias = PackageHelper::getAlias(UnidevPackage::class);

        return Asset::root() . '/' . $alias . '/images/default-img.png';
    }

    /**
     * addBase
     *
     * @param string $url
     *
     * @return  string
     */
    public static function addBase($url)
    {
        if (strpos($url, 'http') === 0 || strpos($url, '//') === 0) {
            return $url;
        }

        return Asset::root() . ltrim($url, '/');
    }
}
