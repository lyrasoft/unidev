"use strict";

function isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _construct(Parent, args, Class) { if (isNativeReflectConstruct()) { _construct = Reflect.construct; } else { _construct = function _construct(Parent, args, Class) { var a = [null]; a.push.apply(a, args); var Constructor = Function.bind.apply(Parent, a); var instance = new Constructor(); if (Class) _setPrototypeOf(instance, Class.prototype); return instance; }; } return _construct.apply(null, arguments); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */
var S3Uploader =
/*#__PURE__*/
function () {
  _createClass(S3Uploader, null, [{
    key: "getInstance",
    value: function getInstance(name) {
      if (!this.instances[name]) {
        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }

        this.instances[name] = _construct(this, [name].concat(args));
      }

      return this.instances[name];
    }
  }]);

  function S3Uploader(name) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

    _classCallCheck(this, S3Uploader);

    this.name = name;
    this.options = $.extend(true, {}, this.constructor.defaultOptions, options);
  }

  _createClass(S3Uploader, [{
    key: "upload",
    value: function upload(file, path) {
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var fileData = new FormData();
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
      }).done(function (res, textStatus, xhr) {
        console.log(xhr);
      }).fail(function (xhr) {
        console.log(xhr);
      }); // console.log(this.options.endpoint + '/' + this.options.subfolder.trim('/') + '/' + path.trim('/'));
    }
  }]);

  return S3Uploader;
}();

_defineProperty(S3Uploader, "defaultOptions", {
  accessKey: '',
  bucket: '',
  signature: '',
  acl: '',
  endpoint: '',
  region: '',
  subfolder: '',
  version: 2
});

_defineProperty(S3Uploader, "instances", {});
//# sourceMappingURL=s3-uploader.js.map
