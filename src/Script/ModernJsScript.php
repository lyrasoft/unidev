<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Script;

use Lyrasoft\Unidev\UnidevPackage;
use Windwalker\Legacy\Core\Asset\AbstractScript;

/**
 * The WebComponent class.
 *
 * @since  1.5.16
 */
class ModernJsScript extends AbstractScript
{
    /**
     * Property packageClass.
     *
     * @var  string
     */
    protected static $packageClass = UnidevPackage::class;

    /**
     * webComponent
     *
     * @return  void
     *
     * @since  1.5.16
     */
    public static function webComponent(): void
    {
        if (!static::inited(__METHOD__)) {
            static::addJS(static::packageName() . '/js/webcomponent/custom-elements-es5-adapter.js');
            static::addJS(static::packageName() . '/js/webcomponent/webcomponents-bundle.js');
        }
    }

    /**
     * litElement
     *
     * @return  void
     *
     * @since  1.5.16
     */
    public static function litElement(): void
    {
        if (!static::inited(__METHOD__)) {
            static::coreJS(true);
            static::webComponent();

            static::addJS(static::packageName() . '/js/webcomponent/lit-element.min.js');
        }
    }

    /**
     * coreJS
     *
     * @param bool $regenerator
     *
     * @return  void
     *
     * @since  1.5.16
     */
    public static function coreJS(bool $regenerator = false): void
    {
        if (!static::inited(__METHOD__)) {
            // All polyfill from babel-polyfill.js
            static::addJS(static::packageName() . '/js/polyfill/core.min.js');
        }

        if ($regenerator) {
            static::regeneratorRuntime();
        }
    }

    /**
     * runtimeGenerator
     *
     * @return  void
     *
     * @since  1.5.16
     */
    public static function regeneratorRuntime(): void
    {
        if (!static::inited(__METHOD__)) {
            // All polyfill from babel-polyfill.js
            static::addJS(static::packageName() . '/js/polyfill/runtime.min.js');
        }
    }
}
