<?php
/**
 * Part of virtualset project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Script;

use Lyrasoft\Unidev\Browser\WhichBrowserFactory;
use Lyrasoft\Unidev\UnidevPackage;
use Phoenix\Script\JQueryScript;
use Phoenix\Script\PhoenixScript;
use Psr\Http\Message\ResponseInterface;
use Windwalker\Core\Asset\AbstractScript;
use Windwalker\Dom\Builder\HtmlBuilder;
use Windwalker\Environment\Browser\Browser;
use Windwalker\Event\Event;
use Windwalker\Http\Stream\Stream;
use Windwalker\Ioc;
use Windwalker\String\Str;
use Windwalker\Utilities\Arr;

/**
 * The EditorScript class.
 *
 * @since  1.0
 */
class UnidevScript extends AbstractScript
{
    /**
     * Property packageClass.
     *
     * @var  string
     */
    protected static $packageClass = UnidevPackage::class;

    /**
     * Official Sweet Alert.
     *
     * @see https://sweetalert.js.org/guides/#installation
     *
     * @param bool $replaceAlert
     * @param int  $version
     *
     * @return void
     */
    public static function sweetAlert($replaceAlert = false, $version = 1)
    {
        if (!static::inited(__METHOD__)) {
            if ($version == 1) {
                static::addJS(static::packageName() . '/js/sweetalert.min.js');
                static::addCSS(static::packageName() . '/css/sweetalert.min.css');
            } else {
                static::polyfill();
                static::addJS(static::packageName() . '/js/sweetalert2.min.js');
            }
        }

        if (!static::inited(__METHOD__, $replaceAlert)) {
            static::internalJS("alert = swal;");
        }
    }

    /**
     * cropit
     *
     * @return  void
     */
    public static function cropit()
    {
        if (!static::inited(__METHOD__)) {
            JQueryScript::core();

            static::addJS(static::packageName() . '/js/jquery.cropit.min.js');
        }
    }

    /**
     * singleDrapUpload
     *
     * @param   string $selector
     * @param   array  $options
     */
    public static function singleImageDragUpload($selector, $options = [])
    {
        $asset = static::getAsset();

        if (!static::inited(__METHOD__)) {
            static::cropit();
            static::sweetAlert(false, 2);

            static::addJS(static::packageName() . '/js/single-image-uploader.min.js');

            PhoenixScript::translate('unidev.field.single.image.message.invalid.image.title');
            PhoenixScript::translate('unidev.field.single.image.message.invalid.image.desc');
            PhoenixScript::translate('unidev.field.single.image.message.invalid.size.title');
            PhoenixScript::translate('unidev.field.single.image.message.invalid.size.desc');

            PhoenixScript::translate('unidev.field.single.image.message.invalid.size.max.width');
            PhoenixScript::translate('unidev.field.single.image.message.invalid.size.min.width');
            PhoenixScript::translate('unidev.field.single.image.message.invalid.size.max.height');
            PhoenixScript::translate('unidev.field.single.image.message.invalid.size.min.height');

            static::internalCSS(<<<CSS
.sid-row::after {
	content: "";
	display: block;
	clear: both;
}

.sid-left-col {
	float: left;
	width: 30%;
	margin-right: 15px;
}

.sid-left-col img {
	max-height: 250px;
}

.sid-right-col {
	overflow: hidden;
}

.filedrag {
	font-weight: bold;
	text-align: center;
	padding: 9% 0;
	color: #ccc;
	border: 2px dashed #ccc;
	border-radius: 7px;
	cursor: default;
}

.filedrag.hover {
	color: #333;
	border-color: #333;
	background-color: #f9f9f9;
}

.cropit-image-background {
	opacity: .2;
}

.sid-size-info {
	margin-top: 5px;
	font-size: 13px;
}

.sid-delete-image {
	margin-left: -17px;
}

/*
 * If the slider or anything else is covered by the background image,
 * use relative or absolute position on it
 */
input.cropit-image-zoom-input {
	position: relative;
}

/* Limit the background image by adding overflow: hidden */
#image-cropper {
	overflow: hidden;
}

.sid-modal .btn {
	position: relative;
}
CSS
            );
        }

        if (!static::inited(__METHOD__, func_get_args())) {
            $options = static::getJSObject($options);

            $asset->internalScript(<<<JS
jQuery(function($) {
    $('$selector').singleImageDragUploader($options);
});
JS
            );
        }
    }

    /**
     * disableTransitionBeforeLoad
     *
     * @param string $className
     *
     * @return  void
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function disableTransitionBeforeLoad(string $className = 'no-transition'): void
    {
        if (!static::inited(__METHOD__)) {
            $css = <<<CSS
.$className * {
  -webkit-transition: none !important;
  -moz-transition: none !important;
  -ms-transition: none !important;
  -o-transition: none !important;
  transition: none !important;
}
CSS;

            static::internalCSS($css);
            static::internalJS("$(function () { $('body').removeClass('$className'); })");
        }
    }

    /**
     * webComponent
     *
     * @param array  $components
     * @param array  $options
     * @param array  $attribs
     *
     * @return  void
     *
     * @since  1.4
     */
    public static function webComponent(array $components, array $options = [], array $attribs = [])
    {
        if (!static::inited(__METHOD__)) {
            static::addJS(static::packageName() . '/js/webcomponent/webcomponents-lite.min.js');
        }

        foreach ($components as $uri) {
            if (Str::endsWith($uri, '.html')) {
                static::import($uri, $options, $attribs);
            } else {
                if (self::inited(static::class . '::babel')) {
                    $attribs['type'] = 'text/babel';
                }

                static::addJS($uri, $options, $attribs);
            }
        }
    }

    /**
     * polyfill
     *
     * @return  void
     *
     * @since  1.3.5
     */
    public static function polyfill()
    {
        if (!static::inited(__METHOD__)) {
            // TODO: Replace all with core.js v3, @see https://github.com/zloirock/core-js/pull/325

            // All polyfill from babel-polyfill.js
            static::addJS(static::packageName() . '/js/polyfill/polyfill.min.js');
        }
    }

    /**
     * coreJS
     *
     * @return  void
     *
     * @since  1.5.13
     */
    public static function coreJS(): void
    {
        if (!static::inited(__METHOD__)) {
            // All polyfill from babel-polyfill.js
            static::addJS(static::packageName() . '/js/polyfill/core.min.js');
        }
    }

    /**
     * babel
     *
     * @param array    $presets
     * @param callable $condition
     *
     * @return  void
     *
     * @since  1.3.5
     */
    public static function babel(array $presets = [], callable $condition = null)
    {
        if (!static::inited(__METHOD__)) {
            $condition = $condition ?: function (Browser $browser) use ($presets) {
                $presets = $presets ?: ['stage-2'];
                array_unshift($presets, 'es2015');

                return array_intersect($presets, ['stage-0', 'stage-1']) !== []
                    || $browser->getBrowser() === $browser::IE;
            };

            if ($condition(Ioc::getEnvironment()->getBrowser())) {
                static::polyfill();

                static::addJS(static::packageName() . '/js/polyfill/babel.min.js');
            }

            // Parse all scripts
            Ioc::getDispatcher()->listen('onBeforeRespond', function (Event $event) use ($presets) {
                /** @var ResponseInterface $response */
                $response = $event['response'];

                if (strpos($response->getHeaderLine('content-type'), 'text/html') === false) {
                    return;
                }

                $body = $response->getBody()->__toString();

                $body = preg_replace_callback(
                    '/<script(.*?)>(.*?)<\/script>/is',
                    function ($matches) use ($presets) {
                        if (isset($matches[1])) {
                            preg_match_all('/(.*?)="(.*?)"/', $matches[1], $matches2, PREG_SET_ORDER);

                            $attrs = [];

                            foreach ($matches2 as $m) {
                                if (isset($m[1], $m[2])) {
                                    $attrs[trim($m[1])] = $m[2];
                                }
                            }

                            if (isset($attrs['type']) && $attrs['type'] === 'text/babel') {
                                $tagPresets = [];
                                $browser    = Ioc::getEnvironment()->getBrowser();

                                $attrs = Arr::def($attrs, 'data-presets', 'es2015,stage-2');

                                if ($attrs['data-presets']) {
                                    $tagPresets = array_map('trim', explode(',', $attrs['data-presets']));
                                }

                                if (array_intersect($tagPresets, ['stage-0', 'stage-1']) === []
                                    && $browser->getBrowser() !== $browser::IE
                                ) {
                                    unset($attrs['type']);
                                }
                            }
                        }

                        return sprintf('<script%s>%s</script>', HtmlBuilder::buildAttributes($attrs), $matches[2]);
                    },
                    $body
                );

                $stream = new Stream('php://temp', 'wb+');
                $stream->write($body);
                $response = $response->withBody($stream);

                $event['response'] = $response;
            });
        }
    }
}
