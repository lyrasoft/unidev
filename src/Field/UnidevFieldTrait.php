<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Field;

/**
 * UnidevFieldTrait
 *
 * @method  SingleImageDragField singleImageDrag($name = null, $label = null)
 *
 * @since  {DEPLOY_VERSION}
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