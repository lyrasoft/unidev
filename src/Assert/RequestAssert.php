<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 .
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Assert;

use Webmozart\Assert\Assert;
use Windwalker\Legacy\Http\Exception\HttpRequestException;

/**
 * The RequestAssert class.
 *
 * @since  1.5.11
 *
 * @deprecated Use \Windwalker\Legacy\Core\Assert\RequestAssert instead.
 */
class RequestAssert extends Assert
{
    /**
     * Property exceptionClass.
     *
     * @var  callable
     */
    public static $exceptionHandler;

    /**
     * Method to get property ExceptionHandler
     *
     * @return  callable
     *
     * @since  1.5.11
     */
    public static function getExceptionHandler(): callable
    {
        return static::$exceptionHandler ?: static function (string $message) {
            throw new HttpRequestException($message, 400);
        };
    }

    /**
     * Method to set property exceptionHandler
     *
     * @param callable $exceptionHandler
     *
     * @return  void
     *
     * @since  1.5.11
     */
    public static function setExceptionHandler(callable $exceptionHandler): void
    {
        static::$exceptionHandler = $exceptionHandler;
    }

    /**
     * reportInvalidArgument
     *
     * @param string $message
     *
     * @return  void
     *
     * @since  1.5.11
     */
    protected static function reportInvalidArgument($message)
    {
        if ($handler = static::getExceptionHandler()) {
            $handler($message);
        }

        parent::reportInvalidArgument($message);
    }
}
