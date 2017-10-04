/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

(function ($)
{
    "use strict";

    var plugin = 'singleImageDragUploader';

    var defaultOptions = {
        width: 300,
        height: 300,
        export_zoom: 1
    };

    /**
     * Init class.
     *
     * @param {jQuery} input
     * @param {Object} options
     *
     * @constructor
     */
    var SingleImageDragUploader = function (input, options)
    {
        this.input = input;
        this.selector = input.selector;
        this.element = $(this.selector + '-wrap');
        this.options = $.extend(true, {},  defaultOptions, options);

        // Input
        this.fileData     = this.element.find(".sid-data");
        this.filedrag     = this.element.find(".sid-area");
        this.fileSelector = this.element.find(".sid-file-select-button");
        this.filePreview  = this.element.find(".sid-preview");
        this.loader       = this.element.find(".sid-loader");

        // Modal
        this.modal      = $(this.selector + '-modal');
        this.cropper    = this.modal.find('.sid-cropper');
        this.saveButton = this.modal.find('.sid-save-button');

        this.bindEvents();
    };

    SingleImageDragUploader.prototype = {
        /**
         * Bind events.
         */
        bindEvents: function ()
        {
            var self = this;

            this.filedrag.on('dragover', function(event)
            {
                event.stopPropagation();
                event.preventDefault();
                $(this).addClass('hover');
            });

            this.filedrag.on('dragleave', function(event)
            {
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
            self.modal.on('hide.bs.modal', function ()
            {
                //$(self.fileSelector).val(null);
            });

            // File drop
            this.filedrag.on("drop", function(event)
            {
                event.stopPropagation();
                event.preventDefault();

                $(this).removeClass('hover');

                var files = event.originalEvent.target.files || event.originalEvent.dataTransfer.files;

                self.handleFileSelect(files[0]);
            });
console.log(this.fileSelector);
            // Selector
            this.fileSelector.on('click', function () {
                var $input = $('<input type="file">');
console.log(this);
                $input.on('change', function(event)
                {
                    var files = event.originalEvent.target.files || event.originalEvent.dataTransfer.files;

                    self.handleFileSelect(files[0])
                });

                $input.click();
            });

            // Save button
            this.saveButton.on('click', function ()
            {
                self.saveImage();
            });
        },

        /**
         * Handle file select and raise cropit.
         *
         * @param {File} file
         */
        handleFileSelect: function (file)
        {
            if (!this.checkFile(file))
            {
                return;
            }

            var self = this;
            var reader = new FileReader;

            reader.onload = function (event)
            {
                if (self.options.crop) {
                    self.cropper.cropit('imageSrc', event.target.result);
                } else {
                    self.saveImage(event.target.result, file.type);
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
        checkFile: function (file)
        {
            var types = [
                'image/jpeg',
                'image/png'
            ];

            types = this.options.allow_types || types;

            if (types.indexOf(file.type, types) < 0)
            {
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
         * Save image to input.
         */
        saveImage: function (image, type)
        {
            type = type || 'image/jpeg';

            image = image || this.cropper.cropit('export', {
                type: type,
                quality: .9,
                originalSize: this.options.origin_size || false
            });

            this.fileData.val(image);
            this.filePreview.attr('src', image);
            this.filePreview.css('display', 'block');

            this.modal.modal('hide');

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
    $.fn[plugin] = function(options)
    {
        if (!$.data(this, "unidev." + plugin))
        {
            $.data(this, "unidev." + plugin, new SingleImageDragUploader(this, options));
        }

        return $.data(this, "unidev." + plugin);
    };

})(jQuery);
