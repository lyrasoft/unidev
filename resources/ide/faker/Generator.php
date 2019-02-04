<?php

namespace Faker;

use Faker\Provider\Base;
use Lyrasoft\Unidev\Faker\UnidevFakerProvider;
use Lyrasoft\Unidev\Helper\PravatarHelper;
use Lyrasoft\Unidev\Helper\UnsplashHelper;

/**
 * @see UnidevFakerProvider
 *
 * @see UnsplashHelper
 *
 * @method string unsplashImage(int $width = 800, int $height = 600, ?string $id = null)
 * @property string unsplashImage
 *
 * @method array  unsplashImages(int $count, int $width = 800, int $height = 600, ?int $id = null)
 * @method string unsplashImagesJson(int $count, int $width = 800, int $height = 600, ?int $id = null): string
 *
 * @see PravatarHelper
 *
 * @method string avatar(int $size = 300, ?string $u = null)
 * @property string avatar
 *
 * @see Base
 *
 * @method mixed passthrough($value)
 */
class Generator
{
    //
}
