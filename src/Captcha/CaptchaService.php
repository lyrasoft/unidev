<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Captcha;

use Windwalker\Core\Cache\RuntimeCacheTrait;
use Windwalker\Core\Config\Config;
use Windwalker\DI\Container;

/**
 * The CaptchaService class.
 *
 * @since  1.5.1
 */
class CaptchaService
{
    use RuntimeCacheTrait;

    /**
     * Property container.
     *
     * @var  Container
     */
    protected $container;

    /**
     * Property config.
     *
     * @var  Config
     */
    protected $config;

    /**
     * CaptchaService constructor.
     *
     * @param Container $container
     * @param Config    $config
     */
    public function __construct(Container $container, Config $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * getDriver
     *
     * @param string $profile
     *
     * @return  CaptchaDriverInterface
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @since  1.5.1
     */
    public function getDriver($profile = null)
    {
        $profile = $profile ?: $this->config->get('unidev.captcha.default', 'none');

        return $this->fetch('driver.' . $profile, function () use ($profile) {
            return $this->createDriver($profile);
        });
    }

    /**
     * createDriver
     *
     * @param string $driver
     *
     * @return  CaptchaDriverInterface
     *
     * @throws \ReflectionException
     * @throws \Windwalker\DI\Exception\DependencyResolutionException
     *
     * @since  1.5.1
     */
    public function createDriver($driver)
    {
        switch ($driver) {
            case 'recaptcha':
                return $this->container->newInstance(RecaptchaDriver::class, [
                    'key' => $this->config->get('unidev.captcha.' . $driver . '.key'),
                    'secret' => $this->config->get('unidev.captcha.' . $driver . '.secret'),
                    'type' => $this->config->get('unidev.captcha.' . $driver . '.type'),
                ]);

            case 'none':
            default:
                return $this->container->newInstance(NullCaptchaDriver::class);
        }
    }
}
