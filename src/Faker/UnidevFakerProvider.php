<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Faker;

use Faker\Provider\Base as BaseProvider;
use Lyrasoft\Unidev\Helper\PravatarHelper;
use Lyrasoft\Unidev\Helper\UnsplashHelper;

/**
 * The UnsplashFakerProvider class.
 *
 * @since  1.5.6
 */
class UnidevFakerProvider extends BaseProvider
{
    /**
     * unsplash
     *
     * @param int      $width
     * @param int      $height
     * @param int|null $id
     *
     * @return  string
     *
     * @since  1.5.6
     */
    public function unsplashImage(int $width = 800, int $height = 600, ?int $id = null): string
    {
        return UnsplashHelper::getImageUrl($width, $height, $id);
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
     * @since  1.5.6
     * @throws \Exception
     */
    public function unsplashImages(int $count, int $width = 800, int $height = 600, ?int $id = null): array
    {
        return UnsplashHelper::getImages($count, $width, $height, $id);
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
     * @since  1.5.6
     * @throws \Exception
     */
    public function unsplashImagesJson(int $count, int $width = 800, int $height = 600, ?int $id = null): string
    {
        return json_encode($this->unsplashImages($count, $width, $height, $id));
    }

    /**
     * avatar
     *
     * @param int         $size
     * @param string|null $u
     *
     * @return  string
     *
     * @throws \Exception
     *
     * @since  __DEPLOY_VERSION__
     */
    public function avatar(int $size = 300, ?string $u = null): string
    {
        return PravatarHelper::unique($size, $u);
    }
}
