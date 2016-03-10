<?php
/**
 * Part of virtualset project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Controller;

use Lyrasoft\Unidev\Buffer\BufferFactory;
use Phoenix\Controller\AbstractPhoenixController;
use Windwalker\Core\Model\Exception\ValidFailException;
use Windwalker\Data\Data;
use Windwalker\Debugger\Helper\DebuggerHelper;

/**
 * The AbstractAjaxController class.
 *
 * @since  {DEPLOY_VERSION}
 */
abstract class AbstractAjaxController extends AbstractPhoenixController
{
	/**
	 * Property format.
	 *
	 * @var  string
	 */
	protected $format = BufferFactory::FORMAT_JSON;

	/**
	 * prepareExecute
	 *
	 * @return  void
	 */
	protected function prepareExecute()
	{
		DebuggerHelper::disableConsole();
	}

	/**
	 * doExecute
	 *
	 * @return  string
	 */
	protected function doExecute()
	{
		try
		{
			$result = $this->doAjax();
		}
		catch (ValidFailException $e)
		{
			$errors = $e->getMessages();

			return $this->responseFailure($e->getMessage(), $e->getCode(), array('errors' => $errors));
		}
		catch (\Exception $e)
		{
			$data = array();

			if (WINDWALKER_DEBUG)
			{
				$traces = array();

				foreach ((array) $e->getTrace() as $trace)
				{
					$trace = new Data($trace);

					$traces[] = array(
						'file' => $trace['file'] ? $trace['file'] . ' (' . $trace['line'] . ')' : null,
						'function' => ($trace['class'] ? $trace['class'] . '::' : null) . $trace['function'] . '()'
					);
				}

				$data['backtrace'] = $traces;
			}

			return $this->responseFailure($e->getMessage(), $e->getCode(), $data);
		}

		return $result;
	}

	/**
	 * doAjax
	 *
	 * @return  mixed
	 */
	abstract protected function doAjax();

	/**
	 * postExecute
	 *
	 * @param   string  $result
	 *
	 * @return  string
	 */
	protected function postExecute($result = null)
	{
		return $result;
	}

	/**
	 * responseSuccess
	 *
	 * @param string $message
	 * @param mixed  $data
	 *
	 * @return  string
	 */
	protected function responseSuccess($message = null, $data = null)
	{
		return $this->response($message, $data, true);
	}

	/**
	 * responseFailure
	 *
	 * @param string $message
	 * @param int    $code
	 * @param mixed  $data
	 *
	 * @return string
	 */
	protected function responseFailure($message = null, $code = 200, $data = null)
	{
		return $this->response($message, $data, false, $code);
	}

	/**
	 * response
	 *
	 * @param string  $message
	 * @param mixed   $data
	 * @param boolean $success
	 * @param int     $code
	 *
	 * @return string
	 */
	protected function response($message = null, $data = null, $success = true, $code = 200)
	{
		/** @var BufferFactory $factory */
		$factory = $this->container->get('unidev.buffer.factory');
		$buffer  = $factory->create($this->format, $message, $data, $success, $code);

		$this->app->response->setMimeType($buffer->getMimeType())->setHeader('STATUS', $code);

		return $buffer;
	}
}
