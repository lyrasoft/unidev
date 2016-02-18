<?php
/**
 * Part of eng4tw project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Buffer;

/**
 * The BufferFactory class.
 *
 * @since  {DEPLOY_VERSION}
 */
class BufferFactory
{
	const FORMAT_JSON = 'json';
	const FORMAT_XML = 'xml';

	/**
	 * create
	 *
	 * @param string $format
	 * @param string $message
	 * @param array  $data
	 * @param bool   $success
	 * @param int    $code
	 *
	 * @return  AbstractBuffer
	 */
	public function create($format = self::FORMAT_JSON, $message = null, $data = array(), $success = true, $code = 200)
	{
		$class = __NAMESPACE__ . '\\' . ucfirst($format) . 'Buffer';

		return new $class($message, $data, $success, $code);
	}
}
