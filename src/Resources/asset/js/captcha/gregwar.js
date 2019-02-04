"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */
$(function () {
  Phoenix.plugin('gregwar',
  /*#__PURE__*/
  function () {
    function GregwarCaptcha($element) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      _classCallCheck(this, GregwarCaptcha);

      this.$element = $element;
      this.options = options;
      this.$image = this.$element.find('[data-captcha-image]');
      this.$input = this.$element.find('[data-captcha-input]');
      this.$refreshButton = this.$element.find('[data-captcha-refresh]');
      this.$buttonIcon = this.$element.find('[data-refresh-icon]');
      this.bindEvents();
    }

    _createClass(GregwarCaptcha, [{
      key: "bindEvents",
      value: function bindEvents() {
        var _this = this;

        this.$refreshButton.on('click', function () {
          _this.refresh();
        });
      }
    }, {
      key: "refresh",
      value: function refresh() {
        var _this2 = this;

        this.$buttonIcon.addClass('fa-spin');
        var src = this.$image.attr('data-image');
        var t = new Date().getTime().toString() + '.' + Math.random() * 10000;

        if (src.indexOf('?') !== -1) {
          src += '&t=' + t;
        } else {
          src += '?t=' + t;
        }

        this.$image.one('load', function () {
          _this2.$buttonIcon.removeClass('fa-spin');

          _this2.$input.val('');
        });
        this.$image.attr('src', src);
      }
    }, {
      key: "clear",
      value: function clear() {
        this.$input.val('');
      }
    }]);

    return GregwarCaptcha;
  }());
});
//# sourceMappingURL=gregwar.js.map
