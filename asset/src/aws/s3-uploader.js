/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

class S3Uploader extends PhoenixEventMixin(class {}) {
  static defaultOptions = {
    accessKey: '',
    bucket: '',
    acl: '',
    endpoint: '',
    region: '',
    subfolder: '',
    signature: '',
    policy: '',
    version: 2,
    starts_with: []
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
    options['Content-Type'] = options['Content-Type'] || 'text/plain';
    options['Content-Disposition'] = options['Content-Disposition'] || '';

    // Prepare default
    for (let key of Object.keys(this.options.starts_with)) {
      fileData.append(key, options[key] || '');
    }

    fileData.append('acl', this.options.acl);
    fileData.append('AWSAccessKeyId', this.options.accessKey);
    fileData.append('policy', this.options.policy);
    fileData.append('signature', this.options.signature);
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
        console.error(xhr);
        this.trigger('fail');
      })
      .always(() => {
        this.trigger('end');
      });
  }

  static trimSlashes(str) {
    return str.replace(/^\/+|\/+$/g, '');
  }
}
