/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

$(() => {
  Phoenix.plugin('gregwar', class GregwarCaptcha {
    constructor($element, options = {}) {
      this.$element = $element;
      this.options = options;
      this.$image = this.$element.find('[data-captcha-image]');
      this.$input = this.$element.find('[data-captcha-input]');
      this.$refreshButton = this.$element.find('[data-captcha-refresh]');
      this.$buttonIcon = this.$element.find('[data-refresh-icon]');

      this.bindEvents();
    }

    bindEvents() {
      this.$refreshButton.on('click', () => {
        this.refresh();
      });
    }

    refresh() {
      this.$buttonIcon.addClass('fa-spin');

      let src = this.$image.attr('data-image');
      const t = (new Date).getTime().toString() + '.' + (Math.random() * 10000);

      if (src.indexOf('?') !== -1) {
        src += '&t=' + t;
      } else {
        src += '?t=' + t;
      }

      this.$image.one('load', () => {
        this.$buttonIcon.removeClass('fa-spin');
        this.$input.val('');
      });

      this.$image.attr('src', src);
    }

    clear() {
      this.$input.val('');
    }
  });
});
