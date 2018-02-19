<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev;

use Lyrasoft\Unidev\Helper\UnidevHelper;
use Phoenix\Language\TranslatorHelper;
use Windwalker\Core\Package\AbstractPackage;

define('UNIDEV_ROOT', __DIR__);

/**
 * The UnidevPackage class.
 *
 * @since  1.0
 */
class UnidevPackage extends AbstractPackage
{
    /**
     * UnidevPackage constructor.
     */
    public function __construct()
    {
        UnidevHelper::setPackage($this);
    }

    /**
     * initialise
     *
     * @throws  \LogicException
     * @return  void
     */
    public function boot()
    {
        parent::boot();

        TranslatorHelper::loadAll($this);
    }
}
