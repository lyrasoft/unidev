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
 * @since  __DEPLOY_VERSION__
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
     * @since  __DEPLOY_VERSION__
     */
    public function input(array $attrs = [], array $options = [])
    {
        return '';
    }

    /**
     * verify
     *
     * @param array $request
     * @param array $options
     *
     * @return  bool
     *
     * @since  __DEPLOY_VERSION__
     */
    public function verify(array $request, array $options = [])
    {
        return true;
    }
}
