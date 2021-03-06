<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Field;

use Lyrasoft\Unidev\Captcha\CaptchaDriverInterface;
use Lyrasoft\Unidev\Captcha\CaptchaService;
use Lyrasoft\Unidev\Captcha\NullCaptchaDriver;
use Windwalker\Core\Repository\Exception\ValidateFailException;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Validate\ValidateResult;
use Windwalker\Ioc;

/**
 * The CaptchaField class.
 *
 * @method  mixed|$this  profile(string $value = null)
 * @method  mixed|$this  autoValidate(bool $value = null)
 * @method  mixed|$this  jsVerify(bool $value = null)
 * @method  mixed|$this  captchaOptions(array $value = null)
 * @method  mixed|$this  imageRoute(string|callable $value = null)
 *
 * @since  1.5.1
 */
class CaptchaField extends TextField
{
    /**
     * Property driver.
     *
     * @var CaptchaDriverInterface
     */
    protected $driver;

    /**
     * prepareRenderInput
     *
     * @param array $attrs
     */
    public function prepare(&$attrs)
    {
        parent::prepare($attrs);
    }

    /**
     * buildInput
     *
     * @param array $attrs
     *
     * @return  mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function buildInput($attrs)
    {
        return $this->getDriver()->input($attrs, $this->getCaptchaOptions());
    }

    /**
     * validate
     *
     * @return  ValidateResult
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function validate()
    {
        if ($this->autoValidate() && !$this->getDriver() instanceof NullCaptchaDriver) {
            if (!$this->getDriver()->verify($this->getValue(), $this->getCaptchaOptions())) {
                throw new ValidateFailException(__('unidev.field.captcha.message.verify.fail'));
            }
        }

        // Do not need required validate
        $this->required(false);

        return parent::validate();
    }

    /**
     * getCaptchaOptions
     *
     * @return  array
     *
     * @since  1.5.2
     */
    protected function getCaptchaOptions()
    {
        $options = $this->captchaOptions();
        $options['js_verify'] = $this->jsVerify();
        $options['profile'] = $this->profile();

        $route = $this->imageRoute();

        if (is_callable($route)) {
            $route = $route($this, $options);
        }

        $options['route'] = $route;

        return $options;
    }

    /**
     * getDriver
     *
     * @return  CaptchaDriverInterface
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @since  1.5.1
     */
    public function getDriver()
    {
        if (!$this->driver) {
            $this->driver = Ioc::getContainer()->get(CaptchaService::class)->getDriver($this->profile());
        }

        return $this->driver;
    }

    /**
     * getAccessors
     *
     * @return  array
     */
    protected function getAccessors()
    {
        return array_merge(
            parent::getAccessors(),
            [
                'profile' => 'profile',
                'autoValidate' => 'auto_validate',
                'jsVerify' => 'js_verify',
                'captchaOptions' => 'captcha_options',
                'imageRoute' => 'image_route',
            ]
        );
    }
}
