<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Captcha;

use Gregwar\Captcha\CaptchaBuilder;
use Lyrasoft\Unidev\UnidevPackage;
use Phoenix\Script\PhoenixScript;
use Windwalker\Core\Asset\AssetManager;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Core\Package\PackageHelper;
use Windwalker\Core\Widget\WidgetHelper;
use Windwalker\Session\Session;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Classes\OptionAccessTrait;

/**
 * The GregwarDriver class.
 *
 * @since  1.5.2
 */
class GregwarDriver implements CaptchaDriverInterface, CaptchaImageInterface
{
    use OptionAccessTrait;

    /**
     * Property session.
     *
     * @var  Session
     */
    protected $session;

    /**
     * Property builder.
     *
     * @var CaptchaBuilder
     */
    protected $builder;

    /**
     * Property asset.
     *
     * @var  AssetManager
     */
    protected $asset;

    /**
     * GregwarDriver constructor.
     *
     * @param Session      $session
     * @param AssetManager $asset
     * @param array        $options
     */
    public function __construct(Session $session, AssetManager $asset, array $options = [])
    {
        if (!class_exists(CaptchaBuilder::class)) {
            throw new \DomainException('Please install gregwar/captcha first.');
        }

        $this->session = $session;
        $this->asset = $asset;
        $this->options = $options;
        $this->builder = new CaptchaBuilder();
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
        $this->asset->addJS(PackageHelper::getAlias(UnidevPackage::class) . '/js/captcha/gregwar.min.js');
        PhoenixScript::domready("$('#{$attrs['id']}-wrapper').gregwar()");

        $attrs['placeholder'] = Arr::get($attrs, 'placeholder') ?: __('unidev.captcha.gregwar.input.placeholder');

        return WidgetHelper::render(
            'unidev.captcha.gregwar',
            [
                'attrs' => $attrs,
                'options' => Arr::mergeRecursive($this->options, $options),
            ],
            'edge'
        );
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
        $key = $this->getOption('session_key', 'captcha');
        $lifetime = $this->getOption('lifetime', 300);

        $content = $this->session->get($key, []);

        // Clear phase instantly
        $this->session->remove($key);

        $phrase = (string) Arr::get($content, 'phrase');
        $time = (int) Arr::get($content, 'time');

        if ($phrase === '' || !$time) {
            return false;
        }

        // Phase check
        if (strtolower($value) !== strtolower($phrase)) {
            return false;
        }

        $now = new Chronos('now');
        $created = new Chronos($time);

        // Is Expired
        return !($created->modify('+' . $lifetime . 'seconds') < $now);
    }

    /**
     * image
     *
     * @return  string
     *
     * @since  1.5.2
     */
    public function image()
    {
        return $this->build()->get($this->getOption('quality', 90));
    }

    /**
     * output
     *
     * @since  1.5.2
     */
    public function output()
    {
        $builder = $this->build();

        header('Content-Type: ' . $this->contentType());

        $builder->output($this->getOption('quality', 90));
    }

    /**
     * base64
     *
     * @return  string
     *
     * @since  1.5.2
     */
    public function base64()
    {
        return $this->build()->inline($this->getOption('quality', 90));
    }

    /**
     * build
     *
     * @return CaptchaBuilder
     *
     * @since  1.5.2
     */
    protected function build()
    {
        $builder = $this->getBuilder();
        $builder->build();

        $key = $this->getOption('session_key', 'captcha');

        $this->session->set($key, [
            'phrase' => $builder->getPhrase(),
            'time' => Chronos::create()->toUnix()
        ]);

        return $builder;
    }

    /**
     * contentType
     *
     * @return  string
     *
     * @since  1.5.2
     */
    public function contentType()
    {
        return 'image/jpeg';
    }

    /**
     * Method to get property Builder
     *
     * @return  CaptchaBuilder
     *
     * @since  1.5.2
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Method to set property builder
     *
     * @param   CaptchaBuilder $builder
     *
     * @return  static  Return self to support chaining.
     *
     * @since  1.5.2
     */
    public function setBuilder(CaptchaBuilder $builder)
    {
        $this->builder = $builder;

        return $this;
    }
}
