<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Captcha;

/**
 * Interface CaptchaDriverInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface CaptchaDriverInterface
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
    public function input(array $attrs = [], array $options = []);

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
    public function verify(array $request, array $options = []);
}
