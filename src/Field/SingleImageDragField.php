<?php
/**
 * Part of virtualset project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Field;

use Lyrasoft\Luna\Helper\LunaHelper;
use Lyrasoft\Unidev\Image\Base64Image;
use Lyrasoft\Unidev\Script\UnidevScript;
use Phoenix\Controller\AbstractSaveController;
use Windwalker\Core\Widget\WidgetHelper;
use Windwalker\Data\DataInterface;
use Windwalker\Dom\HtmlElement;
use Windwalker\Form\Field\TextareaField;
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
 * @method  mixed|$this  showSizeNotice(bool $value = null)
 * @method  mixed|$this  crop(bool $value = null)
 * @method  mixed|$this  originSize(bool $value = null)
 * @method  mixed|$this  exportZoom(string $value = null)
 * @method  mixed|$this  defaultImage(string $value = null)
 * @method  mixed|$this  version(int $value = null)
 * @method  mixed|$this  layout(string $value = null)
 * @method  mixed|$this  ajax(string|bool $value = null)
 * @method  mixed|$this  previewHandler(callable $value = null)
 *
 * @since  1.0
 */
class SingleImageDragField extends TextareaField
{
    /**
     * prepareRenderInput
     *
     * @param array $attrs
     *
     * @return void
     */
    public function prepare(&$attrs)
    {
        $this->appendAttribute('class', ' sid-data');

        parent::prepare($attrs);

        $attrs['value']  = $this->getValue();
        $attrs['width']  = $this->get('width', 300);
        $attrs['height'] = $this->get('height', 300);
        $attrs['type']   = 'text';
        $attrs['style']  = (isset($attrs['style']) ? $attrs['style'] : '') . '; display: none;';
    }

    /**
     * buildInput
     *
     * @param array $attrs
     *
     * @return  mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function buildInput($attrs)
    {
        $options['export_zoom'] = (float) $exportZoom = $this->getAttribute('export_zoom', 1);
        $options['crop']        = $this->getBool('crop', true);
        $options['origin_size'] = $this->getBool('originSize', false);

        $options['width']      = $exportZoom * (int) $this->get('width', 300);
        $options['height']     = $exportZoom * (int) $this->get('height', 300);
        $options['max_width']  = $this->get('max_width');
        $options['min_width']  = $this->get('min_width');
        $options['max_height'] = $this->get('max_height');
        $options['min_height'] = $this->get('min_height');
        $options['version']    = (int) $this->get('version', 1);

        if ($ajax = $this->ajax()) {
            if (is_bool($this->ajax())) {
                if (!class_exists(LunaHelper::class)) {
                    throw new \LogicException('Auto ajax URL must install Luna first, use custom AJAX URL instead.');
                }

                $options['ajax_url'] = LunaHelper::getPackage()
                    ->getCurrentPackage()
                    ->router
                    ->route('_luna_img_upload', ['resize' => 0]);
            } else {
                $options['ajax_url'] = (string) $this->ajax();
            }
        }

        $this->prepareScript($attrs, $options);

        return WidgetHelper::render($this->get('layout', 'unidev.form.field.single-drag-image'), [
            'crop' => $this->get('crop', true),
            'field' => $this,
            'options' => $options,
            'attrs' => $attrs,
            'version' => $options['version'],
            'defaultImage' => $this->get('default_image'),
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
        $selector = '#' . $attrs['id'] . '-wrap';

        $options['modal_target'] = '#' . $attrs['id'] . '-modal';

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
     *
     * @throws \ReflectionException
     * @deprecated Use uploadBase64() instead.
     */
    public static function uploadFromController(AbstractSaveController $controller, $field, DataInterface $data, $uri)
    {
        // formControl is protected, we get it by TestHelper
        $base64 = $controller->input->post->getRaw(
            'input-' . TestHelper::getValue($controller, 'formControl') . '-' . $field . '-data'
        );
        $delete = $controller->input->post->get(
            'input-' . TestHelper::getValue($controller, 'formControl') . '-' . $field . '-delete-image'
        );

        if ($base64 && $url = Base64Image::quickUpload($base64, $uri)) {
            $data->$field = $url;

            return $url;
        }

        if ($delete) {
            $data->$field = '';

            return true;
        }

        return false;
    }

    /**
     * Upload base64 to cloud.
     *
     * You must set SingleDragImageField to version 2.
     *
     * ```
     * $this->singleDragImage('foo')
     *     ->version(2)
     * ```
     *
     * @param string           $base64
     * @param string           $uri
     * @param callable|null    $handler
     * @param string|bool|null $suffix
     *
     * @return string
     * @since   1.3
     */
    public static function uploadBase64($base64, $uri, callable $handler = null, $suffix = null): string
    {
        if (strpos($base64, 'data:image') !== 0) {
            return $base64;
        }

        if ($handler) {
            $base64 = $handler($base64);
        }

        if ($url = Base64Image::quickUpload($base64, $uri)) {
            if ($suffix) {
                if ($suffix === true) {
                    $suffix = bin2hex(time());
                }

                if (strpos($url, '?') !== false) {
                    $url .= '&' . $suffix;
                } else {
                    $url .= '?' . $suffix;
                }
            }

            return $url;
        }

        return $base64;
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
            'id' => $this->getId() . '-view',
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
            'crop',
            'defaultImage' => 'default_image',
            'exportZoom' => 'export_zoom',
            'height',
            'maxHeight' => 'max_height',
            'maxWidth' => 'max_width',
            'minHeight' => 'min_height',
            'minWidth' => 'min_width',
            'showSizeNotice' => 'show_size_notice',
            'previewHandler' => 'preview_handler',
            'originSize',
            'version',
            'width',
            'layout',
            'ajax'
        ]);
    }
}
