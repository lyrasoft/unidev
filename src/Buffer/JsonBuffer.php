<?php
/**
 * Part of virtualset project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Buffer;

/**
 * The JsonResponse class.
 *
 * @since  {DEPLOY_VERSION}
 */
class JsonBuffer extends AbstractBuffer
{
	/**
	 * Method for sending the response in JSON format
	 *
	 * @return  string  The response in JSON format
	 */
	public function toString()
	{
		return json_encode(get_object_vars($this));
	}

	/**
	 * getMimeType
	 *
	 * @return  string
	 */
	public function getMimeType()
	{
		return 'application/json';
	}
}
