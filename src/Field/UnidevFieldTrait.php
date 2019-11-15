<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Field;

/**
 * UnidevFieldTrait
 *
 * @method  SingleImageDragField singleImageDrag($name = null, $label = null)
 * @method  CaptchaField         captcha($name = null, $label = null)
 *
 * @since  1.5.14
 */
trait UnidevFieldTrait
{
    /**
     * bootPhoenixFieldTrait
     *
     * @return  void
     */
    public function bootUnidevFieldTrait()
    {
        $this->addNamespace('Lyrasoft\Unidev\Field');
    }
}
