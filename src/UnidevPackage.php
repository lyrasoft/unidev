<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev;

use Lyrasoft\Unidev\Helper\UnidevHelper;
use Windwalker\Legacy\Core\Language\Translator;
use Windwalker\Legacy\Core\Package\AbstractPackage;

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
     * @return  void
     * @throws \ReflectionException
     * @throws \Windwalker\Legacy\DI\Exception\DependencyResolutionException
     */
    public function boot()
    {
        parent::boot();
        
        $this->getDispatcher()->listen('onPackagePreprocess', function () {
            Translator::loadAll($this);
        });
    }
}
