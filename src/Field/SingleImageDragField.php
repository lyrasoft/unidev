<?php
/**
 * Part of virtualset project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Field;

use Lyrasoft\Unidev\Script\UnidevScript;
use Lyrasoft\Unidev\Image\Base64Image;
use Phoenix\Controller\AbstractSaveController;
use Windwalker\Core\Widget\WidgetHelper;
use Windwalker\Data\Data;
use Windwalker\Data\DataInterface;
use Windwalker\Dom\HtmlElement;
use Windwalker\Form\Field\TextField;
use Windwalker\Test\TestHelper;

/**
 * The SingleImageField class.
 *
 * @since  1.0
 */
class SingleImageDragField extends TextField
{
	/**
	 * prepareRenderInput
	 *
	 * @param array $attrs
	 *
	 * @return  array
	 */
	public function prepare(&$attrs)
	{
		parent::prepare($attrs);

		$attrs['width']  = $this->get('width', 300);
		$attrs['height'] = $this->get('height', 300);
	}

	/**
	 * buildInput
	 *
	 * @param array $attrs
	 *
	 * @return  mixed
	 */
	public function buildInput($attrs)
	{
		$this->prepareScript($attrs);

		return WidgetHelper::render('unidev.form.field.single-drag-image', [
			'attrs' => $attrs,
			'defaultImage' => $this->get('default_image')
		], WidgetHelper::EDGE);
	}

	/**
	 * prepareScript
	 *
	 * @param   array  $attrs
	 *
	 * @return  void
	 */
	protected function prepareScript($attrs)
	{
		$selector = '#' . $attrs['id'];

		$options['export_zoom'] = $exportZoom = $this->getAttribute('export_zoom', 1);
		$options['width']  = $exportZoom * $this->get('width', 300);
		$options['height'] = $exportZoom * $this->get('height', 300);

		UnidevScript::singleImageDragUpload($selector, $options);
	}

	/**
	 * uploadFromController
	 *
	 * @param AbstractSaveController $controller
	 * @param string                 $field
	 * @param DataInterface          $data
	 * @param string                 $uri
	 *
	 * @return  boolean|string
	 */
	public static function uploadFromController(AbstractSaveController $controller, $field, DataInterface $data, $uri)
	{
		// formControl is protected, we get it by TestHelper
		$base64 = $controller->input->post->getRaw('input-' . TestHelper::getValue($controller, 'formControl') . '-' . $field . '-data');
		$delete = $controller->input->post->get('input-' . TestHelper::getValue($controller, 'formControl') . '-' . $field . '-delete-image');

		if ($base64 && $url = Base64Image::quickUpload($base64, $uri))
		{
			$data->$field = $url;

			return $url;
		}
		elseif ($delete)
		{
			$data->$field = '';

			return true;
		}

		return false;
	}

	/**
	 * renderView
	 *
	 * @return  string
	 */
	public function renderView()
	{
		$attribs = [
			'src' => $this->getValue(),
			'id' => $this->getId() . '-view'
		];

		return (string) new HtmlElement('img', null, $attribs);
	}
}
