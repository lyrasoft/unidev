<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

use Windwalker\Core\Asset\AssetInstaller;

include_once __DIR__ . '/../../../autoload.php';

$assets = [
	'babel-polyfill' => [
		'dist/polyfill.js' => 'js/polyfill/babel-polyfill.js',
		'dist/polyfill.min.js' => 'js/polyfill/babel-polyfill.min.js',
	],
	'babel-standalone' => [
		'babel.js' => 'js/polyfill/babel.js',
		'babel.min.js' => 'js/polyfill/babel.min.js',
	],
	'url-polyfill' => [
		'url-polyfill.js' => 'js/polyfill/url-polyfill.js',
		'url-polyfill.min.js' => 'js/polyfill/url-polyfill.min.js',
	],
	'sweetalert' => [
		'dist/sweetalert.min.js' => 'sweetalert2.min.js'
	]
];

$app = new AssetInstaller(
	'unidev',
	__DIR__ . '/../node_modules',
	__DIR__ . '/../src/Resources/asset',
	$assets
);

// @After RequireJS
//$app->addHook('after-requirejs', function (AssetInstaller $app, $vendorName)
//{
//	$app->minify($app->getAssetPath() . '/js/core/require.js');
//});
//
//// @After Punycode
//$app->addHook('after-punycode', function (AssetInstaller $app, $vendorName)
//{
//	$app->minify($app->getAssetPath() . '/js/string/punycode.js');
//});

$app->execute();
