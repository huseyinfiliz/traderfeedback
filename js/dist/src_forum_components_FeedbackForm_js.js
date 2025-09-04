"use strict";
(self["webpackChunkmodule_exports"] = self["webpackChunkmodule_exports"] || []).push([["src_forum_components_FeedbackForm_js"],{

/***/ "./src/forum/components/FeedbackForm.js":
/*!**********************************************!*\
  !*** ./src/forum/components/FeedbackForm.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FeedbackForm)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_common_components_Modal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/common/components/Modal */ "flarum/common/components/Modal");
/* harmony import */ var flarum_common_components_Modal__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Modal__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/common/components/Button */ "flarum/common/components/Button");
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/common/components/Select */ "flarum/common/components/Select");
/* harmony import */ var flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! flarum/common/components/LoadingIndicator */ "flarum/common/components/LoadingIndicator");
/* harmony import */ var flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! flarum/forum/app */ "flarum/forum/app");
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_app__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! flarum/common/utils/Stream */ "flarum/common/utils/Stream");
/* harmony import */ var flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_6__);







var FeedbackForm = /*#__PURE__*/function (_Modal) {
  function FeedbackForm() {
    return _Modal.apply(this, arguments) || this;
  }
  (0,_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(FeedbackForm, _Modal);
  var _proto = FeedbackForm.prototype;
  _proto.oninit = function oninit(vnode) {
    _Modal.prototype.oninit.call(this, vnode);
    this.user = this.attrs.user;
    this.loading = false;
    this.type = flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_6___default()('positive');
    this.role = flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_6___default()('buyer');
    this.comment = flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_6___default()('');
    this.transactionId = flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_6___default()('');
  };
  _proto.className = function className() {
    return 'FeedbackFormModal Modal--medium';
  };
  _proto.title = function title() {
    return flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.title', {
      username: this.user.displayName()
    });
  };
  _proto.content = function content() {
    return m("div", {
      className: "Modal-body"
    }, m("div", {
      className: "Form"
    }, m("div", {
      className: "Form-group"
    }, m("label", null, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.type_label')), m((flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3___default()), {
      value: this.type(),
      options: {
        'positive': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.type_positive'),
        'neutral': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.type_neutral'),
        'negative': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.type_negative')
      },
      onchange: this.type
    })), m("div", {
      className: "Form-group"
    }, m("label", null, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.role_label')), m((flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3___default()), {
      value: this.role(),
      options: {
        'buyer': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.role_buyer'),
        'seller': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.role_seller'),
        'trader': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.role_trader')
      },
      onchange: this.role
    })), m("div", {
      className: "Form-group"
    }, m("label", null, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.transaction_id_label')), m("input", {
      className: "FormControl",
      value: this.transactionId(),
      oninput: m.withAttr('value', this.transactionId),
      placeholder: flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.transaction_id_placeholder')
    })), m("div", {
      className: "Form-group"
    }, m("label", null, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_label')), m("textarea", {
      className: "FormControl",
      value: this.comment(),
      oninput: m.withAttr('value', this.comment),
      placeholder: flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_placeholder'),
      rows: "5"
    })), m("div", {
      className: "Form-group"
    }, flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default().component({
      type: 'submit',
      className: 'Button Button--primary',
      loading: this.loading,
      disabled: !this.comment().trim()
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button')))));
  };
  _proto.onsubmit = function onsubmit(e) {
    var _this = this;
    e.preventDefault();
    if (!this.comment().trim()) {
      return;
    }
    var minLength = flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().forum.attribute('huseyinfiliz.traderfeedback.minLength') || 10;
    var maxLength = flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().forum.attribute('huseyinfiliz.traderfeedback.maxLength') || 1000;
    if (this.comment().length < minLength) {
      flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().alerts.show({
        type: 'error'
      }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.error_too_short', {
        min: minLength
      }));
      return;
    }
    if (this.comment().length > maxLength) {
      flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().alerts.show({
        type: 'error'
      }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.error_too_long', {
        max: maxLength
      }));
      return;
    }
    this.loading = true;
    flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().request({
      method: 'POST',
      url: flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().forum.attribute('apiUrl') + '/trader/feedback',
      body: {
        data: {
          type: 'feedbacks',
          attributes: {
            to_user_id: this.user.id(),
            type: this.type(),
            role: this.role(),
            comment: this.comment(),
            transaction_id: this.transactionId() || null
          }
        }
      }
    }).then(function () {
      flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().alerts.show({
        type: 'success'
      }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.success'));
      _this.hide();

      // Sayfayı yenile
      if (window.location.pathname.includes('/feedbacks')) {
        window.location.reload();
      }
    })["catch"](function (error) {
      _this.loading = false;
      _this.loaded();
      if (error.response && error.response.errors) {
        flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().alerts.show({
          type: 'error'
        }, error.response.errors[0].detail);
      } else {
        flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().alerts.show({
          type: 'error'
        }, 'An error occurred while submitting feedback.');
      }
    });
  };
  return FeedbackForm;
}((flarum_common_components_Modal__WEBPACK_IMPORTED_MODULE_1___default()));


/***/ })

}]);
//# sourceMappingURL=src_forum_components_FeedbackForm_js.js.map