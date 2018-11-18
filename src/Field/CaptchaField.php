<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Field;

use Lyrasoft\Unidev\Captcha\CaptchaDriverInterface;
use Lyrasoft\Unidev\Captcha\CaptchaService;
use Lyrasoft\Unidev\Captcha\NullCaptchaDriver;
use Windwalker\Core\Repository\Exception\ValidateFailException;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Validate\ValidateResult;
use Windwalker\Ioc;
use Windwalker\Utilities\Arr;

/**
 * The CaptchaField class.
 *
 * @method  mixed|$this  driver(string $value = null)
 * @method  mixed|$this  autoValidate(bool $value = null)
 * @method  mixed|$this  jsVerify(bool $value = null)
 *
 * @since  1.5.1
 */
class CaptchaField extends AbstractField
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
     *
     * @return  array
     */
    public function prepare(&$attrs)
    {
        $attrs['name'] = $this->getFieldName();
        $attrs['id'] = $this->getAttribute('id', $this->getId());

        return $attrs;
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
        $this->prepare($attrs);

        return $this->getDriver()->input($attrs, [
            'js_verify' => $this->jsVerify()
        ]);
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
            if (!$this->getDriver()->verify($_POST)) {
                throw new ValidateFailException(__('unidev.field.captcha.message.varify.fail'));
            }
        }
        
        return parent::validate();
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
            $this->driver = Ioc::getContainer()->get(CaptchaService::class)->getDriver($this->driver());
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
                'driver' => 'driver',
                'autoValidate' => 'auto_validate',
                'jsVerify' => 'js_verify',
            ]
        );
    }
}
