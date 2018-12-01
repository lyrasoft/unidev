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
      this.$refreshButton = this.$element.find('[data-captcha-refresh]');

      this.bindEvents();
    }

    bindEvents() {
      this.$refreshButton.on('click', () => {
        let src = this.$image.attr('data-src');
        const t = (new Date).getTime().toString() + '.' + (Math.random() * 10000);

        if (src.indexOf('?') !== -1) {
          src += '&t=' + t;
        } else {
          src += '?t=' + t;
        }

        this.$image.attr('src', src);
      });
    }
  });
});
