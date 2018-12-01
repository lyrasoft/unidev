<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Captcha;

/**
 * The NullCaptchaDriver class.
 *
 * @since  1.5.1
 */
class NullCaptchaDriver implements CaptchaDriverInterface
{
    /**
     * input
     *
     * @param array $attrs
     * @param array $options
     *
     * @return  string
     *
     * @since  1.5.1
     */
    public function input(array $attrs = [], array $options = [])
    {
        return '';
    }

    /**
     * verify
     *
     * @param string $value
     * @param array  $options
     *
     * @return  bool
     *
     * @since  1.5.1
     */
    public function verify($value, array $options = [])
    {
        return true;
    }
}
