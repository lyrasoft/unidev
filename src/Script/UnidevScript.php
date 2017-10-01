<?php
/**
 * Part of virtualset project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Script;

use Lyrasoft\Unidev\UnidevPackage;
use Phoenix\Script\JQueryScript;
use Phoenix\Script\PhoenixScript;
use Windwalker\Core\Asset\AbstractScript;

/**
 * The EditorScript class.
 *
 * @since  1.0
 */
class UnidevScript extends AbstractScript
{
	/**
	 * Property packageClass.
	 *
	 * @var  string
	 */
	protected static $packageClass = UnidevPackage::class;

	/**
	 * sweetAlert
	 *
	 * @return  void
	 */
	public static function sweetAlert($replaceAlert = false)
	{
		if (!static::inited(__METHOD__))
		{
			static::addJS(static::packageName() . '/js/sweetalert.min.js');
			static::addCSS(static::packageName() . '/css/sweetalert.min.css');
		}

		if (!static::inited(__METHOD__, $replaceAlert))
		{
			static::internalJS("alert = swal;");
		}
	}

	/**
	 * cropit
	 *
	 * @return  void
	 */
	public static function cropit()
	{
		if (!static::inited(__METHOD__))
		{
			JQueryScript::core();

			static::addJS(static::packageName() . '/js/jquery.cropit.min.js');
		}
	}

	/**
	 * singleDrapUpload
	 *
	 * @param   string $selector
	 * @param   array  $options
	 */
	public static function singleImageDragUpload($selector, $options = [])
	{
		$asset = static::getAsset();

		if (!static::inited(__METHOD__))
		{
			static::cropit();
			static::sweetAlert();

			static::addJS(static::packageName() . '/js/single-image-uploader.min.js');

			PhoenixScript::translate('unidev.field.single.image.message.invalid.image.title');
			PhoenixScript::translate('unidev.field.single.image.message.invalid.image.desc');
			PhoenixScript::translate('unidev.field.single.image.message.invalid.size.title');
			PhoenixScript::translate('unidev.field.single.image.message.invalid.size.desc');

			static::internalCSS(<<<CSS
.sid-row::after {
	content: "";
	display: block;
	clear: both;
}

.sid-left-col {
	float: left;
	width: 30%;
	margin-right: 15px;
}

.sid-left-col img {
	max-height: 250px;
}

.sid-right-col {
	overflow: hidden;
}

.filedrag {
	font-weight: bold;
	text-align: center;
	padding: 15% 0;
	color: #ccc;
	border: 2px dashed #ccc;
	border-radius: 7px;
	cursor: default;
}

.filedrag.hover {
	color: #333;
	border-color: #333;
	background-color: #f9f9f9;
}

.cropit-image-background {
	opacity: .2;
}

/*
 * If the slider or anything else is covered by the background image,
 * use relative or absolute position on it
 */
input.cropit-image-zoom-input {
	position: relative;
}

/* Limit the background image by adding overflow: hidden */
#image-cropper {
	overflow: hidden;
}

.sid-modal .btn {
	position: relative;
}
CSS
			);
		}

		if (!static::inited(__METHOD__, func_get_args()))
		{
			$options = static::getJSObject($options);

			$asset->internalScript(<<<JS
jQuery(function($) {
    $('$selector').singleImageDragUploader($options);
});
JS
			);
		}
	}
}
