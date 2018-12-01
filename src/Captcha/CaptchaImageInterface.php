<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Captcha;

/**
 * Interface CaptchaImageInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface CaptchaImageInterface
{
    /**
     * image
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function image();

    /**
     * output
     *
     * @since  __DEPLOY_VERSION__
     */
    public function output();

    /**
     * base64
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function base64();

    /**
     * contentType
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public function contentType();
}
