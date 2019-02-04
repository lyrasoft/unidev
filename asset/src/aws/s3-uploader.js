/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

class S3Uploader {
  static defaultOptions = {
    accessKey: '',
    bucket: '',
    signature: '',
    acl: '',
    endpoint: '',
    region: '',
    subfolder: '',
    version: 2
  };

  static instances = {};

  static getInstance(name, ...args) {
    if (!this.instances[name]) {
      this.instances[name] = new this(name, ...args);
    }

    return this.instances[name];
  }

  constructor(name, options = {}) {
    this.name = name;
    this.options = $.extend(true, {}, this.constructor.defaultOptions, options);
  }

  upload(file, path, options = {}) {
    const fileData = new FormData();

    fileData.append('key', this.options.subfolder.trim('/') + '/' + path.trim('/'));
    fileData.append('acl', this.options.acl);
    fileData.append('AWSAccessKeyId', this.options.accessKey);
    fileData.append('policy', this.options.policy);
    fileData.append('signature', this.options.signature);
    fileData.append('Content-type', file.type);
    fileData.append('Content-Disposition', '');
    fileData.append('file', file);

    $.post({
        url: this.options.endpoint,
        data: fileData,
        processData: false,
        contentType: false,
        type: 'POST'
      })
      .done((res,  textStatus, xhr) => {
        console.log(xhr);
      })
      .fail((xhr) => {
        console.log(xhr);
      });

    // console.log(this.options.endpoint + '/' + this.options.subfolder.trim('/') + '/' + path.trim('/'));
  }
}
