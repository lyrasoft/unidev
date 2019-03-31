"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _construct(Parent, args, Class) { if (isNativeReflectConstruct()) { _construct = Reflect.construct; } else { _construct = function _construct(Parent, args, Class) { var a = [null]; a.push.apply(a, args); var Constructor = Function.bind.apply(Parent, a); var instance = new Constructor(); if (Class) _setPrototypeOf(instance, Class.prototype); return instance; }; } return _construct.apply(null, arguments); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */
var S3Uploader =
/*#__PURE__*/
function (_PhoenixEventMixin) {
  _inherits(S3Uploader, _PhoenixEventMixin);

  _createClass(S3Uploader, null, [{
    key: "getInstance",

    /**
     * @param {string} name
     * @param {*}      args
     *
     * @returns {S3Uploader}
     */
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
    var _this;

    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

    _classCallCheck(this, S3Uploader);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(S3Uploader).call(this));
    _this.name = name;
    _this.options = $.extend(true, {}, _this.constructor.defaultOptions, options);
    return _this;
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


  _createClass(S3Uploader, [{
    key: "upload",
    value: function upload(file, path) {
      var _this2 = this;

      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var fileData = new FormData();
      var inputs = $.extend({}, this.options.formInputs);

      if (typeof file === 'string') {
        file = new Blob([file], {
          type: options['Content-Type'] || 'text/plain'
        });
      }

      if (file instanceof Blob || file instanceof File) {
        options['Content-Type'] = options['Content-Type'] || file.type;
      }

      if (options['filename']) {
        options['Content-Disposition'] = 'attachment; filename*=UTF-8\'\'' + encodeURIComponent(options['filename']);
      }

      options['key'] = this.constructor.trimSlashes(this.options.subfolder) + '/' + this.constructor.trimSlashes(path);
      options['key'] = this.constructor.trimSlashes(options['key']);
      options['Content-Type'] = options['Content-Type'] || null;
      options['Content-Disposition'] = options['Content-Disposition'] || null; // Prepare pre-signed data

      for (var key in inputs) {
        fileData.set(key, inputs[key]);
      } // Prepare custom data


      var _arr = Object.keys(this.options.starts_with);

      for (var _i = 0; _i < _arr.length; _i++) {
        var _key2 = _arr[_i];

        if (options[_key2]) {
          fileData.set(_key2, options[_key2]);
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
        xhr: function xhr() {
          var xhr = new XMLHttpRequest();

          if (xhr.upload) {
            xhr.upload.addEventListener('progress', function (e) {
              _this2.trigger('upload-progress', e);

              if (e.lengthComputable) {
                _this2.trigger('progress', e.loaded / e.total, e);
              }
            }, false);
          }

          return xhr;
        }
      }).done(function (res, textStatus, xhr) {
        var url = _this2.options.endpoint + '/' + _this2.constructor.trimSlashes(_this2.options.subfolder) + '/' + _this2.constructor.trimSlashes(path);

        _this2.trigger('success', url, xhr);
      }).fail(function (xhr) {
        _this2.trigger('fail', xhr);
      }).always(function () {
        _this2.trigger('end');
      });
    }
  }], [{
    key: "trimSlashes",
    value: function trimSlashes(str) {
      return str.replace(/^\/+|\/+$/g, '');
    }
  }]);

  return S3Uploader;
}(PhoenixEventMixin(
/*#__PURE__*/
function () {
  function _class() {
    _classCallCheck(this, _class);
  }

  return _class;
}()));

_defineProperty(S3Uploader, "defaultOptions", {
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
    'X-Amz-Signature': ''
  }
});

_defineProperty(S3Uploader, "instances", {});
//# sourceMappingURL=s3-uploader.js.map
