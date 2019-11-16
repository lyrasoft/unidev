/**
 * Part of fusion project.
 *
 * @copyright  Copyright (C) 2018 Asikart.
 * @license    MIT
 */

const fusion = require('windwalker-fusion');
const BebelHelper = require('windwalker-fusion/src/helpers/BebelHelper');

// The task `css`
fusion.task('css', function () {
  // Watch start
  fusion.watch([
    'asset/scss/**/*.scss'
  ]);
  // Watch end

  // Compile Start
  fusion.sass('asset/scss/**/*.scss', 'src/Resources/asset/css/');
  // Compile end
});

// The task `js`
fusion.task('js', function () {
  // Watch start
  fusion.watch([
    'asset/src/**/*.js'
  ]);
  // Watch end

  // Compile Start
  fusion.babel('asset/src/**/*.js', 'src/Resources/asset/js/');
  // Compile end
});

fusion.task('ws', function () {
  // Watch start
  fusion.watch([
    'asset/ws/**/*.js'
  ]);
  // Watch end

  // Compile Start
  fusion.webpack('asset/ws/**/*.js', 'src/Resources/asset/js/webcomponent/', {
    webpack: {
      // devtool: 'eval-source-map',
      mode: process.env.NODE_ENV || 'development',
      output: {
        filename: '[name].js',
        sourceMapFilename: '[name].js.map'
      },
      stats: {
        all: false,
        errors: true,
        warnings: true,
        version: false,
      },
      module: {
        rules: [
          {
            test: /\.m?js$/,
            // Fis LitElement issue, @see https://github.com/Polymer/lit-element/issues/54#issuecomment-439824447
            exclude: /node_modules\/(?!(lit-html|@polymer)\/).*/,
            use: [{
              loader: 'babel-loader',
              options: BebelHelper.basicOptions()
            }, 'webpack-comment-remover-loader']
          }
        ]
      },
      plugins: []
    }
  });
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

  fusion.copy([
    'node_modules/regenerator-runtime/runtime.js'
  ], 'src/Resources/asset/js/polyfill/');

  fusion.js('src/Resources/asset/js/polyfill/runtime.js');

  fusion.copy(`${nodePath}/@babel/standalone/*.js`, `${destPath}/polyfill/`);
  fusion.copy(`${nodePath}/core-js-bundle/index.js`, `${destPath}/polyfill/core.js`);

  fusion.js(`${destPath}/polyfill/core.js`);

  fusion.js([
    `${nodePath}/@babel/polyfill/dist/*.js`,
    `${nodePath}/url-polyfill/url-polyfill*.js`,
    `${nodePath}/nodelist-foreach-polyfill/index.js`,
  ], `${destPath}/polyfill/polyfill.js`);
  fusion.copy(`${nodePath}/sweetalert/dist/sweetalert.min.js`, `${destPath}/sweetalert2.min.js`);
});

fusion.default(['css', 'js']);

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
