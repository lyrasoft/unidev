<?php
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Helper;

use Windwalker\Legacy\Filesystem\File;
use Windwalker\Legacy\Http\HttpClient;

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
    public static function getImageUrl($width = 800, $height = 600, $id = null): string
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
     * @param int       $count  Images number.
     * @param int|array $width  Can be int or array as random [start, end].
     * @param int|array $height Can be int or array as random [start, end].
     * @param int       $id     Image id or let fetch random id.
     *
     * @return  array
     *
     * @since  1.4
     * @throws \Exception
     */
    public static function getImages($count, $width = 800, $height = 600, $id = null): array
    {
        $images = [];

        foreach (range(1, $count) as $i) {
            $images[] = static::getImageUrl(
                is_array($width) ? random_int(...$width) : $width,
                is_array($height) ? random_int(...$height) : $height,
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
     * @throws \Exception
     */
    public static function getImagesJson($count, $width = 800, $height = 600, $id = null): string
    {
        return json_encode(static::getImages($count, $width, $height, $id));
    }

    /**
     * init
     *
     * @return  array
     */
    protected static function init(): array
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
    protected static function getTempPath(): string
    {
        return WINDWALKER_TEMP . '/unidev/images/picsum-list.data';
    }

    /**
     * getList
     *
     * @return  array
     */
    public static function getList(): array
    {
        if (!static::$images) {
            $http     = new HttpClient();
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
    public static function dump(): void
    {
        $list = static::getList();

        $ids = array_column($list, 'id');

        $content = implode(',', $ids);

        File::write(static::getTempPath(), $content);
    }
}
