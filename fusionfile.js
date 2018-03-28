/**
 * Part of fusion project.
 *
 * @copyright  Copyright (C) 2018 Asikart.
 * @license    MIT
 */

const fusion = require('windwalker-fusion');

// The task `main`
fusion.task('main', function () {
  // Watch start
  fusion.watch([
    'src/Resources/asset/js/single-image-uploader.js'
  ]);
  // Watch end

  // Compile Start
  fusion.js('src/Resources/asset/js/single-image-uploader.js');
  // Compile end
});

// The task `install`
fusion.task('install', function () {
  const nodePath = 'node_modules';
  const destPath = 'src/Resources/asset/js';

  fusion.copy([
    'node_modules/@webcomponents/webcomponentsjs/*.js',
    'node_modules/@webcomponents/webcomponentsjs/*.map',
    '!**/gulpfile.js'
  ], 'src/Resources/asset/js/webcomponent/');

  fusion.copy(`${nodePath}/babel-standalone/*.js`, `${destPath}/polyfill/`);
  fusion.js([
    `${nodePath}/babel-polyfill/dist/*.js`,
    `${nodePath}/url-polyfill/url-polyfill*.js`,
    `${destPath}/polyfill/node-list-loop.js`
  ], `${destPath}/polyfill/polyfill.js`);
  fusion.copy(`${nodePath}/sweetalert/dist/sweetalert.min.js`, `${destPath}/sweetalert2.min.js`);
});

fusion.default(['main']);

/*
 * APIs
 *
 * Compile entry:
 * fusion.js(source, dest, options = {})
 * fusion.babel(source, dest, options = {})
 * fusion.ts(source, dest, options = {})
 * fusion.typeScript(source, dest, options = {})
 * fusion.css(source, dest, options = {})
 * fusion.less(source, dest, options = {})
 * fusion.sass(source, dest, options = {})
 * fusion.copy(source, dest, options = {})
 *
 * Live Reload:
 * fusion.livereload(source, dest, options = {})
 * fusion.reload(file)
 *
 * Gulp proxy:
 * fusion.src(source, options)
 * fusion.dest(path, options)
 * fusion.task(name, deps, fn)
 * fusion.watch(glob, opt, fn)
 *
 * Stream Helper:
 * fusion.through(handler) // Same as through2.obj()
 *
 * Config:
 * fusion.disableNotification()
 * fusion.enableNotification()
 */
