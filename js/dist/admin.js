/******/ (() => { // webpackBootstrap
/******/ 	// runtime can't be in strict mode because a global variable is assign and maybe created.
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js ***!
  \******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _inheritsLoose)
/* harmony export */ });
/* harmony import */ var _setPrototypeOf_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./setPrototypeOf.js */ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js");

function _inheritsLoose(t, o) {
  t.prototype = Object.create(o.prototype), t.prototype.constructor = t, (0,_setPrototypeOf_js__WEBPACK_IMPORTED_MODULE_0__["default"])(t, o);
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _setPrototypeOf)
/* harmony export */ });
function _setPrototypeOf(t, e) {
  return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) {
    return t.__proto__ = e, t;
  }, _setPrototypeOf(t, e);
}


/***/ }),

/***/ "./src/admin/components/TraderFeedbackApprovalsPage.js":
/*!*************************************************************!*\
  !*** ./src/admin/components/TraderFeedbackApprovalsPage.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ TraderFeedbackApprovalsPage)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_common_Component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/common/Component */ "flarum/common/Component");
/* harmony import */ var flarum_common_Component__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_common_Component__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/common/components/Button */ "flarum/common/components/Button");
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/common/components/LoadingIndicator */ "flarum/common/components/LoadingIndicator");
/* harmony import */ var flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var flarum_common_helpers_avatar__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! flarum/common/helpers/avatar */ "flarum/common/helpers/avatar");
/* harmony import */ var flarum_common_helpers_avatar__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(flarum_common_helpers_avatar__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! flarum/common/helpers/username */ "flarum/common/helpers/username");
/* harmony import */ var flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! flarum/common/helpers/humanTime */ "flarum/common/helpers/humanTime");
/* harmony import */ var flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! flarum/admin/app */ "flarum/admin/app");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_app__WEBPACK_IMPORTED_MODULE_7__);








//import m from 'mithril'; // Import mithril
var TraderFeedbackApprovalsPage = /*#__PURE__*/function (_Component) {
  function TraderFeedbackApprovalsPage() {
    return _Component.apply(this, arguments) || this;
  }
  (0,_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(TraderFeedbackApprovalsPage, _Component);
  var _proto = TraderFeedbackApprovalsPage.prototype;
  _proto.oninit = function oninit(vnode) {
    _Component.prototype.oninit.call(this, vnode);
    this.loading = true;
    this.feedbacks = [];
    this.loadFeedbacks();
  };
  _proto.loadFeedbacks = function loadFeedbacks() {
    var _this = this;
    this.loading = true;
    flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().request({
      method: 'GET',
      url: flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().forum.attribute('apiUrl') + '/trader/feedback/pending'
    }).then(function (response) {
      _this.feedbacks = response.data;
      _this.loading = false;
      m.redraw();
    })["catch"](function (error) {
      _this.loading = false;
      m.redraw();
    });
  };
  _proto.view = function view() {
    var _this2 = this;
    if (this.loading) {
      return m("div", {
        className: "TraderFeedbackApprovalsPage"
      }, m((flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3___default()), null));
    }
    return m("div", {
      className: "TraderFeedbackApprovalsPage"
    }, m("div", {
      className: "container"
    }, m("h2", null, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.approvals.title')), this.feedbacks.length === 0 ? m("div", {
      className: "TraderFeedbackApprovalsPage-empty"
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.approvals.no_approvals')) : m("div", {
      className: "TraderFeedbackApprovalsPage-list"
    }, this.feedbacks.map(function (feedback) {
      return _this2.feedbackItem(feedback);
    }))));
  };
  _proto.feedbackItem = function feedbackItem(feedback) {
    var _this3 = this;
    return m("div", {
      className: "TraderFeedbackApprovalsPage-item " + feedback.type,
      key: feedback.id
    }, m("div", {
      className: "TraderFeedbackApprovalsPage-item-header"
    }, m("div", {
      className: "TraderFeedbackApprovalsPage-item-user"
    }, flarum_common_helpers_avatar__WEBPACK_IMPORTED_MODULE_4___default()(feedback.fromUser), flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5___default()(feedback.fromUser), " \u2192 ", flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5___default()(feedback.toUser)), m("div", {
      className: "TraderFeedbackApprovalsPage-item-date"
    }, flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_6___default()(feedback.created_at))), m("div", {
      className: "TraderFeedbackApprovalsPage-item-meta"
    }, m("span", {
      className: "TraderFeedbackApprovalsPage-item-type " + feedback.type
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans("huseyinfiliz-traderfeedback.forum.form.type_" + feedback.type)), m("span", {
      className: "TraderFeedbackApprovalsPage-item-role"
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans("huseyinfiliz-traderfeedback.forum.feedback_item.as_" + feedback.role))), m("div", {
      className: "TraderFeedbackApprovalsPage-item-comment"
    }, feedback.comment), m("div", {
      className: "TraderFeedbackApprovalsPage-item-actions"
    }, m((flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default()), {
      className: "Button Button--primary",
      onclick: function onclick() {
        return _this3.approveFeedback(feedback);
      }
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.approvals.approve_button')), m((flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default()), {
      className: "Button Button--danger",
      onclick: function onclick() {
        return _this3.rejectFeedback(feedback);
      }
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.approvals.reject_button'))));
  };
  _proto.approveFeedback = function approveFeedback(feedback) {
    var _this4 = this;
    if (confirm(flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.approvals.confirm_approve'))) {
      flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().request({
        method: 'POST',
        url: flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/approve'
      }).then(function () {
        _this4.loadFeedbacks();
      });
    }
  };
  _proto.rejectFeedback = function rejectFeedback(feedback) {
    var _this5 = this;
    if (confirm(flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.approvals.confirm_reject'))) {
      flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().request({
        method: 'POST',
        url: flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().forum.attribute('apiUrl') + '/trader/feedback/' + feedback.id + '/reject'
      }).then(function () {
        _this5.loadFeedbacks();
      });
    }
  };
  return TraderFeedbackApprovalsPage;
}((flarum_common_Component__WEBPACK_IMPORTED_MODULE_1___default()));


/***/ }),

/***/ "./src/admin/components/TraderFeedbackReportsPage.js":
/*!***********************************************************!*\
  !*** ./src/admin/components/TraderFeedbackReportsPage.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ TraderFeedbackReportsPage)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_common_Component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/common/Component */ "flarum/common/Component");
/* harmony import */ var flarum_common_Component__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_common_Component__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/common/components/Button */ "flarum/common/components/Button");
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/common/components/LoadingIndicator */ "flarum/common/components/LoadingIndicator");
/* harmony import */ var flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var flarum_common_helpers_avatar__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! flarum/common/helpers/avatar */ "flarum/common/helpers/avatar");
/* harmony import */ var flarum_common_helpers_avatar__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(flarum_common_helpers_avatar__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! flarum/common/helpers/username */ "flarum/common/helpers/username");
/* harmony import */ var flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! flarum/common/helpers/humanTime */ "flarum/common/helpers/humanTime");
/* harmony import */ var flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! flarum/admin/app */ "flarum/admin/app");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_app__WEBPACK_IMPORTED_MODULE_7__);








//import m from 'mithril'; // Import mithril
var TraderFeedbackReportsPage = /*#__PURE__*/function (_Component) {
  function TraderFeedbackReportsPage() {
    return _Component.apply(this, arguments) || this;
  }
  (0,_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(TraderFeedbackReportsPage, _Component);
  var _proto = TraderFeedbackReportsPage.prototype;
  _proto.oninit = function oninit(vnode) {
    _Component.prototype.oninit.call(this, vnode);
    this.loading = true;
    this.reports = [];
    this.loadReports();
  };
  _proto.loadReports = function loadReports() {
    var _this = this;
    this.loading = true;
    flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().request({
      method: 'GET',
      url: flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().forum.attribute('apiUrl') + '/trader/reports'
    }).then(function (response) {
      _this.reports = response.data;
      _this.loading = false;
      m.redraw();
    })["catch"](function (error) {
      _this.loading = false;
      m.redraw();
    });
  };
  _proto.view = function view() {
    var _this2 = this;
    if (this.loading) {
      return m("div", {
        className: "TraderFeedbackReportsPage"
      }, m((flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3___default()), null));
    }
    return m("div", {
      className: "TraderFeedbackReportsPage"
    }, m("div", {
      className: "container"
    }, m("h2", null, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.title')), this.reports.length === 0 ? m("div", {
      className: "TraderFeedbackReportsPage-empty"
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.no_reports')) : m("div", {
      className: "TraderFeedbackReportsPage-list"
    }, this.reports.map(function (report) {
      return _this2.reportItem(report);
    }))));
  };
  _proto.reportItem = function reportItem(report) {
    var _this3 = this;
    var feedback = report.feedback;
    var reporter = report.user;
    return m("div", {
      className: "TraderFeedbackReportsPage-item",
      key: report.id
    }, m("div", {
      className: "TraderFeedbackReportsPage-item-header"
    }, m("div", {
      className: "TraderFeedbackReportsPage-item-user"
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.user_reported', {
      username: flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5___default()(reporter)
    })), m("div", {
      className: "TraderFeedbackReportsPage-item-date"
    }, flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_6___default()(report.created_at))), m("div", {
      className: "TraderFeedbackReportsPage-item-reason"
    }, m("strong", null, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.forum.report_modal.reason_label'), ":"), " ", report.reason), m("div", {
      className: "TraderFeedbackReportsPage-item-feedback"
    }, m("div", {
      className: "TraderFeedbackReportsPage-item-feedback-header"
    }, m("div", {
      className: "TraderFeedbackReportsPage-item-feedback-user"
    }, flarum_common_helpers_avatar__WEBPACK_IMPORTED_MODULE_4___default()(feedback.fromUser), flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5___default()(feedback.fromUser), " \u2192 ", flarum_common_helpers_username__WEBPACK_IMPORTED_MODULE_5___default()(feedback.toUser)), m("div", {
      className: "TraderFeedbackReportsPage-item-feedback-type"
    }, feedback.type)), m("div", {
      className: "TraderFeedbackReportsPage-item-feedback-comment"
    }, feedback.comment)), m("div", {
      className: "TraderFeedbackReportsPage-item-actions"
    }, m((flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default()), {
      className: "Button Button--primary",
      onclick: function onclick() {
        return _this3.approveReport(report);
      }
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.approve_button')), m((flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default()), {
      className: "Button Button--danger",
      onclick: function onclick() {
        return _this3.rejectReport(report);
      }
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.reject_button')), m((flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default()), {
      className: "Button",
      onclick: function onclick() {
        return _this3.dismissReport(report);
      }
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.dismiss_button'))));
  };
  _proto.approveReport = function approveReport(report) {
    var _this4 = this;
    if (confirm(flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.confirm_approve'))) {
      flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().request({
        method: 'POST',
        url: flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/approve'
      }).then(function () {
        _this4.loadReports();
      });
    }
  };
  _proto.rejectReport = function rejectReport(report) {
    var _this5 = this;
    if (confirm(flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.confirm_reject'))) {
      flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().request({
        method: 'POST',
        url: flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/reject'
      }).then(function () {
        _this5.loadReports();
      });
    }
  };
  _proto.dismissReport = function dismissReport(report) {
    var _this6 = this;
    if (confirm(flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().translator.trans('huseyinfiliz-traderfeedback.admin.reports.confirm_dismiss'))) {
      flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().request({
        method: 'POST',
        url: flarum_admin_app__WEBPACK_IMPORTED_MODULE_7___default().forum.attribute('apiUrl') + '/trader/reports/' + report.id + '/dismiss'
      }).then(function () {
        _this6.loadReports();
      });
    }
  };
  return TraderFeedbackReportsPage;
}((flarum_common_Component__WEBPACK_IMPORTED_MODULE_1___default()));


/***/ }),

/***/ "./src/admin/components/TraderFeedbackSettingsPage.js":
/*!************************************************************!*\
  !*** ./src/admin/components/TraderFeedbackSettingsPage.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ TraderFeedbackSettingsPage)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_admin_components_ExtensionPage__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/admin/components/ExtensionPage */ "flarum/admin/components/ExtensionPage");
/* harmony import */ var flarum_admin_components_ExtensionPage__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_components_ExtensionPage__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_common_components_Switch__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/common/components/Switch */ "flarum/common/components/Switch");
/* harmony import */ var flarum_common_components_Switch__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Switch__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/common/components/Button */ "flarum/common/components/Button");
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! flarum/admin/app */ "flarum/admin/app");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_app__WEBPACK_IMPORTED_MODULE_4__);





var TraderFeedbackSettingsPage = /*#__PURE__*/function (_ExtensionPage) {
  function TraderFeedbackSettingsPage() {
    return _ExtensionPage.apply(this, arguments) || this;
  }
  (0,_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(TraderFeedbackSettingsPage, _ExtensionPage);
  var _proto = TraderFeedbackSettingsPage.prototype;
  _proto.oninit = function oninit(vnode) {
    _ExtensionPage.prototype.oninit.call(this, vnode);
    this.settings = {
      'huseyinfiliz.traderfeedback.allowNegative': this.setting('huseyinfiliz.traderfeedback.allowNegative')(),
      'huseyinfiliz.traderfeedback.requireApproval': this.setting('huseyinfiliz.traderfeedback.requireApproval')(),
      'huseyinfiliz.traderfeedback.minLength': this.setting('huseyinfiliz.traderfeedback.minLength')(),
      'huseyinfiliz.traderfeedback.maxLength': this.setting('huseyinfiliz.traderfeedback.maxLength')()
    };
  };
  _proto.content = function content() {
    var _this = this;
    return m("div", {
      className: "TraderFeedbackSettingsPage"
    }, m("div", {
      className: "container"
    }, m("form", {
      onsubmit: this.onsubmit.bind(this)
    }, m("div", {
      className: "Form-group"
    }, m("label", null, flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.title')), m("p", {
      className: "helpText"
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.help_text')), m("div", {
      className: "Form-group"
    }, m((flarum_common_components_Switch__WEBPACK_IMPORTED_MODULE_2___default()), {
      state: this.settings['huseyinfiliz.traderfeedback.allowNegative'],
      onchange: function onchange(value) {
        return _this.settings['huseyinfiliz.traderfeedback.allowNegative'] = value;
      }
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.allow_negative_label'))), m("div", {
      className: "Form-group"
    }, m((flarum_common_components_Switch__WEBPACK_IMPORTED_MODULE_2___default()), {
      state: this.settings['huseyinfiliz.traderfeedback.requireApproval'],
      onchange: function onchange(value) {
        return _this.settings['huseyinfiliz.traderfeedback.requireApproval'] = value;
      }
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_approval_label'))), m("div", {
      className: "Form-group"
    }, m("label", null, flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label')), m("input", {
      className: "FormControl",
      type: "number",
      min: "1",
      value: this.settings['huseyinfiliz.traderfeedback.minLength'],
      oninput: function oninput(e) {
        return _this.settings['huseyinfiliz.traderfeedback.minLength'] = e.target.value;
      }
    })), m("div", {
      className: "Form-group"
    }, m("label", null, flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label')), m("input", {
      className: "FormControl",
      type: "number",
      min: "1",
      value: this.settings['huseyinfiliz.traderfeedback.maxLength'],
      oninput: function oninput(e) {
        return _this.settings['huseyinfiliz.traderfeedback.maxLength'] = e.target.value;
      }
    })), m((flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_3___default()), {
      type: "submit",
      className: "Button Button--primary",
      loading: this.loading
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.submit_button'))))));
  };
  _proto.onsubmit = function onsubmit(e) {
    var _this2 = this;
    e.preventDefault();
    this.loading = true;

    // Save settings
    Object.keys(this.settings).forEach(function (key) {
      _this2.setting(key)(_this2.settings[key]);
    });
    flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().alerts.show({
      type: 'success'
    }, flarum_admin_app__WEBPACK_IMPORTED_MODULE_4___default().translator.trans('core.admin.settings.saved_message'));
    this.loading = false;
  };
  return TraderFeedbackSettingsPage;
}((flarum_admin_components_ExtensionPage__WEBPACK_IMPORTED_MODULE_1___default()));


/***/ }),

/***/ "./src/admin/index.js":
/*!****************************!*\
  !*** ./src/admin/index.js ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/admin/app */ "flarum/admin/app");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_app__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_TraderFeedbackSettingsPage__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/TraderFeedbackSettingsPage */ "./src/admin/components/TraderFeedbackSettingsPage.js");
/* harmony import */ var _components_TraderFeedbackReportsPage__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/TraderFeedbackReportsPage */ "./src/admin/components/TraderFeedbackReportsPage.js");
/* harmony import */ var _components_TraderFeedbackApprovalsPage__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/TraderFeedbackApprovalsPage */ "./src/admin/components/TraderFeedbackApprovalsPage.js");




flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().initializers.add('huseyinfiliz-traderfeedback', function () {
  // Register settings page
  flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().extensionData["for"]('huseyinfiliz-traderfeedback').registerSetting({
    setting: 'huseyinfiliz.traderfeedback.allowNegative',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.allow_negative_label'),
    type: 'boolean'
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.requireApproval',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_approval_label'),
    type: 'boolean'
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.minLength',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label'),
    type: 'number'
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.maxLength',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label'),
    type: 'number'
  }).registerPermission({
    icon: 'fas fa-exchange-alt',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.permissions.give_feedback'),
    permission: 'huseyinfiliz-traderfeedback.giveFeedback'
  }, 'reply').registerPermission({
    icon: 'fas fa-exchange-alt',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.permissions.moderate_feedback'),
    permission: 'trader.moderateFeedback'
  }, 'moderate');
});

/***/ }),

/***/ "flarum/admin/app":
/*!**************************************************!*\
  !*** external "flarum.core.compat['admin/app']" ***!
  \**************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['admin/app'];

/***/ }),

/***/ "flarum/admin/components/ExtensionPage":
/*!***********************************************************************!*\
  !*** external "flarum.core.compat['admin/components/ExtensionPage']" ***!
  \***********************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['admin/components/ExtensionPage'];

/***/ }),

/***/ "flarum/common/Component":
/*!*********************************************************!*\
  !*** external "flarum.core.compat['common/Component']" ***!
  \*********************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/Component'];

/***/ }),

/***/ "flarum/common/components/Button":
/*!*****************************************************************!*\
  !*** external "flarum.core.compat['common/components/Button']" ***!
  \*****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/Button'];

/***/ }),

/***/ "flarum/common/components/LoadingIndicator":
/*!***************************************************************************!*\
  !*** external "flarum.core.compat['common/components/LoadingIndicator']" ***!
  \***************************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/LoadingIndicator'];

/***/ }),

/***/ "flarum/common/components/Switch":
/*!*****************************************************************!*\
  !*** external "flarum.core.compat['common/components/Switch']" ***!
  \*****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/Switch'];

/***/ }),

/***/ "flarum/common/helpers/avatar":
/*!**************************************************************!*\
  !*** external "flarum.core.compat['common/helpers/avatar']" ***!
  \**************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/helpers/avatar'];

/***/ }),

/***/ "flarum/common/helpers/humanTime":
/*!*****************************************************************!*\
  !*** external "flarum.core.compat['common/helpers/humanTime']" ***!
  \*****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/helpers/humanTime'];

/***/ }),

/***/ "flarum/common/helpers/username":
/*!****************************************************************!*\
  !*** external "flarum.core.compat['common/helpers/username']" ***!
  \****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/helpers/username'];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!******************!*\
  !*** ./admin.ts ***!
  \******************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _src_admin__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./src/admin */ "./src/admin/index.js");

})();

module.exports = __webpack_exports__;
/******/ })()
;
//# sourceMappingURL=admin.js.map