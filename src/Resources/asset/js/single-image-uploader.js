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
        this.fileSelector = this.element.find(".sid-selector");
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
                    swal('Warning', error.message + ' Please upload a ' + self.options.width + ' x ' + self.options.height + ' image.', 'warning');
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
                $(self.fileSelector).val(null);
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

            // Selector
            this.fileSelector.on('change', function(event)
            {
                var files = event.originalEvent.target.files || event.originalEvent.dataTransfer.files;

                self.handleFileSelect(files[0])
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
                self.cropper.cropit('imageSrc', event.target.result);
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

            if (types.indexOf(file.type, types) < 0)
            {
                swal('Not a image', 'Please select jpg or png file', 'error');

                return false;
            }

            return true;
        },

        /**
         * Save image to input.
         */
        saveImage: function ()
        {
            var image = this.cropper.cropit('export', {
                type: 'image/jpeg',
                quality: .9,
                originalSize: false
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
