/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

(function($) {
  "use strict";

  var plugin = 'singleImageDragUploader';

  var defaultOptions = {
    crop: true,
    export_zoom: 1,
    height: 300,
    max_height: null,
    max_width: null,
    min_height: null,
    min_width: null,
    origin_size: false,
    version: 1,
    width: 300,
    modal_target: ''
  };

  /**
   * Init class.
   *
   * @param {jQuery} element
   * @param {Object} options
   *
   * @constructor
   */
  var SingleImageDragUploader = function(element, options) {
    this.element = element;
    this.options = $.extend(true, {}, defaultOptions, options);

    // Input
    this.fileData = this.element.find('.sid-data');
    this.filedrag = this.element.find('.sid-area');
    this.fileSelector = this.element.find('.sid-file-select-button');
    this.filePreview = this.element.find('.sid-preview');
    this.fileLoader = this.element.find('.sid-img-loader');
    this.deleteBox = this.element.find('.sid-delete-image');
    this.loader = this.element.find('.sid-loader');

    // Modal
    this.modal = $(this.options.modal_target);
    this.cropper = this.modal.find('.sid-cropper');
    this.saveButton = this.modal.find('.sid-save-button');

    this.bindEvents();
  };

  SingleImageDragUploader.prototype = {
    /**
     * Bind events.
     */
    bindEvents: function() {
      var self = this;
      var value;

      this.filedrag.on('dragover', function(event) {
        event.stopPropagation();
        event.preventDefault();
        $(this).addClass('hover');
      });

      this.filedrag.on('dragleave', function(event) {
        event.stopPropagation();
        event.preventDefault();
        $(this).removeClass('hover');
      });

      this.cropper.cropit({
        // imageState: {src: event.target.result},
        imageBackground: true,
        exportZoom: this.options.export_zoom,
        onImageError: function(error) {
          swal(
            Phoenix.Translator.translate('unidev.field.single.image.message.invalid.size.title'),
            Phoenix.Translator.sprintf('unidev.field.single.image.message.invalid.size.desc', self.options.width, self.options.height),
            'warning'
          );
          self.loader.hide();
        },
        onImageLoaded: function() {
          self.modal.modal('show');
          self.loader.hide();
        },
        onImageLoading: function() {
          self.loader.show();
        }
      });

      // Reset file input if modal closed
      self.modal.on('hide.bs.modal', function() {
        //$(self.fileSelector).val(null);
      });

      // File drop
      this.filedrag.on("drop", function(event) {
        event.stopPropagation();
        event.preventDefault();

        $(this).removeClass('hover');

        var files = event.originalEvent.target.files || event.originalEvent.dataTransfer.files;

        self.handleFileSelect(files[0]);
      });

      // Selector
      this.fileSelector.on('click', function() {
        var $input = $('<input type="file">');

        $input.on('change', function(event) {
          var files = event.originalEvent.target.files || event.originalEvent.dataTransfer.files;

          self.handleFileSelect(files[0])
        });

        $input.click();
      });

      // Save button
      this.saveButton.on('click', function() {
        self.saveImage();
      });

      // Delete box
      if (this.options.version !== 1) {
        this.deleteBox.on('change', function() {
          var $this = $(this);

          if ($this.is(':checked')) {
            value = self.fileData.val();
            self.fileData.val('');
          } else {
            self.fileData.val(value);
            value = null;
          }
        });
      }
    },

    /**
     * Handle file select and raise cropit.
     *
     * @param {File} file
     */
    handleFileSelect: function(file) {
      if (!this.checkFile(file)) {
        return;
      }

      var self = this;
      var reader = new FileReader;

      reader.onload = function(event) {
        if (self.options.crop) {
          self.cropper.cropit('imageSrc', event.target.result);
        } else {
          var image = new Image;

          image.onload = function() {
            if (!self.checkSize(image)) {
              return;
            }

            self.saveImage(event.target.result, file.type);
          };

          image.src = event.target.result;
        }
      };

      reader.readAsDataURL(file);
    },

    /**
     * Check file type is image.
     *
     * @param {File} file
     *
     * @returns {boolean}
     */
    checkFile: function(file) {
      var types = [
        'image/jpeg',
        'image/png',
        'image/webp',
      ];

      types = this.options.allow_types || types;

      if (types.indexOf(file.type, types) < 0) {
        swal(
          Phoenix.Translator.translate('unidev.field.single.image.message.invalid.image.title'),
          Phoenix.Translator.translate('unidev.field.single.image.message.invalid.image.desc'),
          'error'
        );

        return false;
      }

      return true;
    },

    /**
     * Check image size.
     *
     * @param {Image} image
     *
     * @returns {boolean}
     */
    checkSize: function(image) {
      try {
        if (this.options.max_width !== null && this.options.max_width < image.width) {
          throw new Error(Phoenix.Translator.sprintf('unidev.field.single.image.message.invalid.size.max.width', this.options.max_width));
        }

        if (this.options.min_width !== null && this.options.min_width > image.width) {
          throw new Error(Phoenix.Translator.sprintf('unidev.field.single.image.message.invalid.size.min.width', this.options.min_width));
        }

        if (this.options.max_height !== null && this.options.max_height < image.height) {
          throw new Error(Phoenix.Translator.sprintf('unidev.field.single.image.message.invalid.size.max.height', this.options.max_height));
        }

        if (this.options.min_height !== null && this.options.min_height > image.height) {
          throw new Error(Phoenix.Translator.sprintf('unidev.field.single.image.message.invalid.size.min.height', this.options.min_height));
        }
      } catch (e) {
        swal(
          Phoenix.Translator.translate('unidev.field.single.image.message.invalid.size.title'),
          e.message,
          'error'
        );

        return false;
      }

      return true;
    },

    /**
     * Save image to input.
     */
    saveImage: function(image, type) {
      var self = this;
      type = type || 'image/jpeg';

      image = image || this.cropper.cropit('export', {
        type: type,
        quality: .9,
        originalSize: this.options.origin_size || false
      });

      this.modal.modal('hide');

      if (this.options.ajax_url) {
        this.filePreview.attr('src', '');
        this.filePreview.hide();
        this.fileLoader.show();

        this.uploadImage(image)
          .done(function (res) {
            self.storeValue(res.data.url);
          })
          .always(function () {
            self.fileLoader.hide();
          });

        return;
      }

      self.storeValue(image);
    },

    uploadImage: function (image) {
      var data = {
        file: image,
        format: 'base64'
      };

      return $.post(this.options.ajax_url, data);
    },

    storeValue: function (image) {
      this.fileData.val(image);
      this.filePreview.attr('src', image);
      this.filePreview.show();

      // Make delete box unchecked
      this.deleteBox.prop('checked', false);

      // Trigger change
      this.fileData.trigger('change');
      this.filePreview.trigger('change');
    }
  };

  /**
   * Push plugins.
   *
   * @param options
   *
   * @returns {*}
   */
  $.fn[plugin] = function(options) {
    if (!$.data(this, "unidev." + plugin)) {
      $.data(this, "unidev." + plugin, new SingleImageDragUploader(this, options));
    }

    return $.data(this, "unidev." + plugin);
  };

})(jQuery);
