<?php
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Helper;

use Windwalker\Filesystem\File;
use Windwalker\Http\HttpClient;
use Windwalker\Utilities\ArrayHelper;

/**
 * The UnsplashHelper class.
 *
 * @since  1.0
 */
class UnsplashHelper
{
    /**
     * Property images.
     *
     * @var  array
     */
    protected static $images = [];

    /**
     * Property ids.
     *
     * @var  array
     */
    protected static $ids = null;

    /**
     * getImageUrl
     *
     * @param int $width
     * @param int $height
     * @param int $id
     *
     * @return  string
     */
    public static function getImageUrl($width = 800, $height = 600, $id = null)
    {
        static::init();

        if (!isset(static::$ids[$id])) {
            $id = static::$ids[array_rand(static::$ids)];
        }

        return 'https://picsum.photos/' . $width . '/' . $height . '?image=' . $id;
    }

    /**
     * getImages
     *
     * @param int       $count   Images number.
     * @param int|array $width   Can be int or array as random [start, end].
     * @param int|array $height  Can be int or array as random [start, end].
     * @param int       $id      Image id or let fetch random id.
     *
     * @return  array
     *
     * @since  1.4
     */
    public static function getImages($count, $width = 800, $height = 600, $id = null)
    {
        $images = [];

        foreach (range(1, $count) as $i) {
            $images[] = static::getImageUrl(
                is_array($width) ? mt_rand(...$width) : $width,
                is_array($height) ? mt_rand(...$height) : $height,
                $id
            );
        }

        return $images;
    }

    /**
     * getImages
     *
     * @param int $count
     * @param int $width
     * @param int $height
     * @param int $id
     *
     * @return  string
     *
     * @since  1.4
     */
    public static function getImagesJson($count, $width = 800, $height = 600, $id = null)
    {
        return json_encode(static::getImages($count, $width, $height, $id));
    }

    /**
     * init
     *
     * @return  array
     */
    protected static function init()
    {
        if (static::$ids === null) {
            $file = static::getTempPath();

            if (!is_file($file)) {
                static::dump();
            }

            $content = file_get_contents($file);

            static::$ids = explode(',', $content);
        }

        return static::$ids;
    }

    /**
     * getTempPath
     *
     * @return  string
     */
    protected static function getTempPath()
    {
        return WINDWALKER_TEMP . '/unidev/images/picsum-list.data';
    }

    /**
     * getList
     *
     * @return  array
     */
    public static function getList()
    {
        if (!static::$images) {
            $http     = new HttpClient;
            $response = $http->get('https://picsum.photos/list');

            $images = json_decode($response->getBody()->__toString());

            foreach ($images as $image) {
                static::$images[$image->id] = $image;
            }
        }

        return static::$images;
    }

    /**
     * dump
     *
     * @return  void
     */
    public static function dump()
    {
        $list = static::getList();

        $ids = ArrayHelper::getColumn($list, 'id');

        $content = implode(',', $ids);

        File::write(static::getTempPath(), $content);
    }
}
