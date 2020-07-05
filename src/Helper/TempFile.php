<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2020 .
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Helper;

use Psr\Http\Message\StreamInterface;
use Windwalker\Filesystem\File;
use Windwalker\Filesystem\Folder;
use Windwalker\Http\Stream\Stream;

/**
 * The TempHelper class.
 *
 * @since  1.5.20
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
     * @since  1.5.20
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
     * @param bool        $autoRemove
     *
     * @return  string
     *
     * @throws \Exception
     * @since  1.5.20
     */
    public static function fileName(?string $name = null, ?string $root = null, bool $autoRemove = true): string
    {
        $file = static::folder($root);

        if ($name === null) {
            $name = md5(uniqid('Windwalker', true));
        }

        $file .= '/' . $name;

        if ($autoRemove) {
            register_shutdown_function(static function () use ($file) {
                if (file_exists($file) && is_file($file)) {
                    File::delete($file);
                }
            });
        }

        return $file;
    }

    /**
     * fileResource
     *
     * @param string|null $name
     * @param string|null $root
     * @param bool        $autoRemove
     *
     * @return  false|resource
     *
     * @throws \Exception
     * @since  1.5.20
     */
    public static function fileResource(?string $name = null, ?string $root = null, bool $autoRemove = true)
    {
        $file = static::fileName($name, $root, $autoRemove);

        return fopen($file, 'wb+');
    }

    /**
     * fileStream
     *
     * @param string|null $name
     * @param string|null $root
     * @param bool        $autoRemove
     *
     * @return  StreamInterface
     *
     * @throws \Exception
     * @since  1.5.20
     */
    public static function fileStream(
        ?string $name = null,
        ?string $root = null,
        bool $autoRemove = true
    ): StreamInterface {
        return new Stream(static::fileName($name, $root, $autoRemove), Stream::MODE_READ_WRITE_RESET);
    }
}
