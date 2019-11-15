<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Captcha;

/**
 * Interface CaptchaImageInterface
 *
 * @since  1.5.2
 */
interface CaptchaImageInterface
{
    /**
     * image
     *
     * @return  string
     *
     * @since  1.5.2
     */
    public function image();

    /**
     * output
     *
     * @since  1.5.2
     */
    public function output();

    /**
     * base64
     *
     * @return  string
     *
     * @since  1.5.2
     */
    public function base64();

    /**
     * contentType
     *
     * @return  string
     *
     * @since  1.5.2
     */
    public function contentType();
}
