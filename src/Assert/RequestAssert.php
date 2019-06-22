<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 .
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Assert;

use Webmozart\Assert\Assert;
use Windwalker\Http\Exception\HttpRequestException;

/**
 * The RequestAssert class.
 *
 * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
     */
    protected static function reportInvalidArgument($message)
    {
        if ($handler = static::getExceptionHandler()) {
            $handler($message);
        }

        parent::reportInvalidArgument($message);
    }
}
