<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Captcha;

use Lyrasoft\Unidev\Captcha\Recaptcha\WindwalkerRequestMethod;
use Phoenix\Script\PhoenixScript;
use ReCaptcha\ReCaptcha;
use Windwalker\Core\Asset\Asset;
use Windwalker\Dom\HtmlElement;
use Windwalker\Utilities\Arr;

/**
 * The RecaptchaDriver class.
 *
 * @since  1.5.1
 */
class RecaptchaDriver implements CaptchaDriverInterface
{
    /**
     * Property key.
     *
     * @var string
     */
    protected $key;

    /**
     * Property secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Property recaptcha.
     *
     * @var ReCaptcha
     */
    protected $recaptcha;

    /**
     * Property type.
     *
     * @var  string
     */
    protected $type;

    /**
     * RecaptchaDriver constructor.
     *
     * @param string $key
     * @param string $secret
     * @param string $type
     */
    public function __construct($key, $secret, $type = 'checkbox')
    {
        if (!class_exists(ReCaptcha::class)) {
            throw new \DomainException('Please install google/recaptcha first.');
        }

        $this->key = $key;
        $this->secret = $secret;
        $this->type = $type;
    }

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
        Asset::addJS('https://www.google.com/recaptcha/api.js');

        $attrs = Arr::def($attrs, 'class', 'g-recaptcha');

        $attrs['data-sitekey'] = $this->key;
        $attrs['class'] = 'g-recaptcha';

        if ($this->type === 'invisible') {
            $attrs['data-size'] = $this->type;
        }

        if ($this->type === 'invisible' || !empty($options['js_verify'])) {
            $attrs = Arr::def($attrs, 'data-callback', 'recaptchaCallback_' . str_replace('-', '_', $attrs['id']));
        }

        if (!empty($options['js_verify'])) {
            $this->prepareScript($attrs['id'], $attrs);
        }

        return (string) new HtmlElement(
            'div',
            null,
            $attrs
        );
    }

    /**
     * verify
     *
     * @param string $value
     * @param array  $options
     *
     * @return bool
     *
     * @since  1.5.1
     */
    public function verify($value, array $options = [])
    {
        $code = Arr::get($_POST, 'g-recaptcha-response');

        $server = Arr::get($options, 'server', $_SERVER);
        $ip = Arr::get($server, 'REMOTE_ADDR');

        $recaptcha = $this->getRecaptchaInstance();

        $response = $recaptcha->verify($code, $ip);

        return $response->isSuccess();
    }

    /**
     * prepareScript
     *
     * @param string $id
     * @param array  $attrs
     *
     * @return  void
     *
     * @since  1.5.1
     */
    protected function prepareScript($id, array $attrs)
    {
        if ($this->type === 'invisible') {
            $js = <<<JS
var input = \$('#$id');
var form = input.parents('form');

form.on('submit', (e) => {
    if (form.data('pass-captcha')) {
        return;
    }

    e.preventDefault();
    e.stopPropagation();

    grecaptcha.execute();
});

window.{$attrs['data-callback']} = function() {
    form.data('pass-captcha', true).submit();
}
JS;
        } else {
            $alert = __('unidev.field.captcha.message.please.check.first');

            $js = <<<JS
var input = \$('#$id');
var form = input.parents('form');

form.on('submit', (e) => {
    if (form.data('pass-captcha')) {
        return;
    }

    e.preventDefault();
    e.stopPropagation();

    alert('$alert');
});

window.{$attrs['data-callback']} = function(response) {
    form.data('pass-captcha', true);
}
JS;
        }

        PhoenixScript::domready($js);
    }

    /**
     * getRecaptchaInstance
     *
     * @return  ReCaptcha
     *
     * @since  1.5.1
     */
    public function getRecaptchaInstance()
    {
        if (!$this->recaptcha) {
            $this->recaptcha = new ReCaptcha($this->secret, new WindwalkerRequestMethod());
        }

        return $this->recaptcha;
    }
}
