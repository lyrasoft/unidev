/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

class S3Uploader extends PhoenixEventMixin(class {}) {
  static defaultOptions = {
    endpoint: '',
    subfolder: '',
    starts_with: [],
    formInputs: {
      acl: '',
      bucket: '',
      key: '',
      Policy: '',
      'X-Amz-Algorithm': '',
      'X-Amz-Credential': '',
      'X-Amz-Date': '',
      'X-Amz-Signature': '',
    }
  };

  static instances = {};

  /**
   * @param {string} name
   * @param {*}      args
   *
   * @returns {S3Uploader}
   */
  static getInstance(name, ...args) {
    if (!this.instances[name]) {
      this.instances[name] = new this(name, ...args);
    }

    return this.instances[name];
  }

  constructor(name, options = {}) {
    super();

    this.name = name;
    this.options = $.extend(true, {}, this.constructor.defaultOptions, options);
  }

  /**
   * Do upload.
   *
   * @param {string|File|Blob} file
   * @param {string}           path
   * @param {Object}           options
   *
   * @returns {Promise}
   */
  upload(file, path, options = {}) {
    const fileData = new FormData();
    const inputs = $.extend({}, this.options.formInputs);

    if (typeof file === 'string') {
      file = new Blob([file], {type: options['Content-Type'] || 'text/plain'});
    }

    if (file instanceof Blob || file instanceof File) {
      options['Content-Type'] = options['Content-Type'] || file.type;
    }

    if (options['filename']) {
      options['Content-Disposition'] = 'attachment; filename*=UTF-8\'\'' + encodeURIComponent(options['filename']);
    }

    options['key'] = this.constructor.trimSlashes(this.options.subfolder) + '/'
      + this.constructor.trimSlashes(path);
    options['key'] = this.constructor.trimSlashes(options['key']);
    options['Content-Type'] = options['Content-Type'] || null;
    options['Content-Disposition'] = options['Content-Disposition'] || null;

    // Prepare pre-signed data
    for (let key in inputs) {
      fileData.set(key, inputs[key]);
    }

    // Prepare custom data
    for (let key of Object.keys(this.options.starts_with)) {
      if (options[key]) {
        fileData.set(key, options[key]);
      }
    }

    fileData.append('file', file);

    this.trigger('start', fileData);

    return $.post({
        url: this.options.endpoint,
        data: fileData,
        processData: false,
        contentType: false,
        type: 'POST',
        xhr: () => {
          const xhr = new XMLHttpRequest();

          if(xhr.upload){
            xhr.upload.addEventListener('progress', e => {
              this.trigger('upload-progress', e);

              if (e.lengthComputable) {
                this.trigger('progress', e.loaded / e.total, e);
              }
            }, false);
          }

          return xhr;
        },
      })
      .done((res, textStatus, xhr) => {
        const url = this.options.endpoint + '/'
          + this.constructor.trimSlashes(this.options.subfolder) + '/'
          + this.constructor.trimSlashes(path);

        this.trigger('success', url, xhr);
      })
      .fail((xhr) => {
        this.trigger('fail', xhr);
      })
      .always(() => {
        this.trigger('end');
      });
  }

  static trimSlashes(str) {
    return str.replace(/^\/+|\/+$/g, '');
  }
}
