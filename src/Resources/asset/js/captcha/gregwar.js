'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

$(function () {
  Phoenix.plugin('gregwar', function () {
    function GregwarCaptcha($element) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      _classCallCheck(this, GregwarCaptcha);

      this.$element = $element;
      this.options = options;
      this.$image = this.$element.find('[data-captcha-image]');
      this.$refreshButton = this.$element.find('[data-captcha-refresh]');

      this.bindEvents();
    }

    _createClass(GregwarCaptcha, [{
      key: 'bindEvents',
      value: function bindEvents() {
        var _this = this;

        this.$refreshButton.on('click', function () {
          var src = _this.$image.attr('data-src');
          var t = new Date().getTime().toString() + '.' + Math.random() * 10000;

          if (src.indexOf('?') !== -1) {
            src += '&t=' + t;
          } else {
            src += '?t=' + t;
          }

          _this.$image.attr('src', src);
        });
      }
    }]);

    return GregwarCaptcha;
  }());
});
//# sourceMappingURL=gregwar.js.map
