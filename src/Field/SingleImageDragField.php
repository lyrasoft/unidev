<?php
/**
 * Part of virtualset project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Field;

use Lyrasoft\Unidev\Image\Base64Image;
use Lyrasoft\Unidev\Script\UnidevScript;
use Phoenix\Controller\AbstractSaveController;
use Windwalker\Core\Widget\WidgetHelper;
use Windwalker\Data\DataInterface;
use Windwalker\Dom\HtmlElement;
use Windwalker\Form\Field\TextField;
use Windwalker\Test\TestHelper;

/**
 * The SingleImageField class.
 *
 * @method  mixed|$this  width(int $value = null)
 * @method  mixed|$this  height(int $value = null)
 * @method  mixed|$this  maxWidth(int $value = null)
 * @method  mixed|$this  minWidth(int $value = null)
 * @method  mixed|$this  maxHeight(int $value = null)
 * @method  mixed|$this  minHeight(int $value = null)
 * @method  mixed|$this  crop(bool $value = null)
 * @method  mixed|$this  originSize(bool $value = null)
 * @method  mixed|$this  exportZoom(string $value = null)
 * @method  mixed|$this  defaultImage(string $value = null)
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
		$options['export_zoom'] = (int) $exportZoom = $this->getAttribute('export_zoom', 1);
		$options['crop'] = $this->getBool('crop', true);
		$options['origin_size'] = $this->getBool('originSize', false);

		$options['width']  = $exportZoom * (int) $this->get('width', 300);
		$options['height'] = $exportZoom * (int) $this->get('height', 300);
		$options['max_width'] = $this->get('max_width');
		$options['min_width'] = $this->get('min_width');
		$options['max_height'] = $this->get('max_height');
		$options['min_height'] = $this->get('min_height');

		$this->prepareScript($attrs, $options);

		return WidgetHelper::render('unidev.form.field.single-drag-image', [
			'crop'  => $this->get('crop', true),
			'field' => $this,
			'options' => $options,
			'attrs' => $attrs,
			'defaultImage' => $this->get('default_image')
		], WidgetHelper::EDGE);
	}

	/**
	 * prepareScript
	 *
	 * @param   array $attrs
	 * @param array   $options
	 *
	 * @return void
	 */
	protected function prepareScript($attrs, array $options)
	{
		$selector = '#' . $attrs['id'];

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

	/**
	 * getAccessors
	 *
	 * @return  array
	 *
	 * @since   3.1.2
	 */
	protected function getAccessors()
	{
		return array_merge(parent::getAccessors(), [
			'width',
			'height',
			'maxWidth' => 'max_width',
			'minWidth' => 'min_width',
			'maxHeight' => 'max_height',
			'minHeight' => 'min_height',
			'crop',
			'originSize',
			'exportZoom' => 'export_zoom',
			'defaultImage' => 'default_image'
		]);
	}
}
