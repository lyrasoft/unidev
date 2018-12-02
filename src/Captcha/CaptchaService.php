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
use Windwalker\Structure\Structure;

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
     * getDefaultProfile
     *
     * @return  string
     *
     * @since  1.5.2
     */
    public function getDefaultProfile()
    {
        return $this->config->get('unidev.captcha.default', 'none');
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
        $profile = $profile ?: $this->getDefaultProfile();

        return $this->fetch('driver.' . $profile, function () use ($profile) {
            return $this->createDriver($profile);
        });
    }

    /**
     * createDriver
     *
     * @param string $profileName
     *
     * @return  CaptchaDriverInterface
     *
     * @throws \ReflectionException
     * @throws \Windwalker\DI\Exception\DependencyResolutionException
     *
     * @since  1.5.1
     */
    public function createDriver($profileName)
    {
        $profile = $this->config->get('unidev.captcha.' . $profileName);

        $profile = new Structure($profile);

        $driver = $profile->get('driver');

        switch ($driver) {
            case 'recaptcha':
                return $this->container->newInstance(RecaptchaDriver::class, [
                    'key' => $profile->get('key'),
                    'secret' => $profile->get('secret'),
                    'type' => $profile->get('type'),
                ]);

            case 'gregwar':
                return $this->container->newInstance(GregwarDriver::class, [
                    'options' => $profile->toArray()
                ]);

            case 'none':
            default:
                return $this->container->newInstance(NullCaptchaDriver::class);
        }
    }
}
