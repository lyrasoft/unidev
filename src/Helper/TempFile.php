<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2020 .
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Helper;

use Psr\Http\Message\StreamInterface;
use Windwalker\Filesystem\Folder;
use Windwalker\Http\Stream\Stream;

/**
 * The TempHelper class.
 *
 * @since  __DEPLOY_VERSION__
 */
class TempFile
{
    /**
     * folder
     *
     * @param string|null $root
     *
     * @return  string
     *
     * @throws \Exception
     * @since  __DEPLOY_VERSION__
     */
    public static function folder(?string $root = null): string
    {
        $folder = sprintf(
            '%s/%s',
            $root ?? WINDWALKER_TEMP,
            chronos()->format('Y/m/d')
        );

        Folder::create($folder);

        return $folder;
    }

    /**
     * fileName
     *
     * @param string|null $name
     * @param string|null $root
     *
     * @return  string
     *
     * @throws \Exception
     * @since  __DEPLOY_VERSION__
     */
    public static function fileName(?string $name = null, ?string $root = null): string
    {
        $file = static::folder($root);

        if ($name === null) {
            $name = md5(uniqid('Windwalker', true));
        }

        $file .= '/' . $name;

        return $file;
    }

    /**
     * fileResource
     *
     * @param string|null $name
     * @param string|null $root
     *
     * @return  false|resource
     *
     * @throws \Exception
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function fileResource(?string $name = null, ?string $root = null)
    {
        $file = static::fileName($name, $root);

        return fopen($file, 'rb+');
    }

    /**
     * fileStream
     *
     * @param string|null $name
     * @param string|null $root
     *
     * @return  StreamInterface
     *
     * @throws \Exception
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function fileStream(?string $name = null, ?string $root = null): StreamInterface
    {
        return new Stream(static::fileName($name, $root), Stream::MODE_READ_WRITE_FROM_BEGIN);
    }
}
