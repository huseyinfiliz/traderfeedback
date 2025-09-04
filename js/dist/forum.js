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

/***/ "./src/forum/Pages/ProfilePage.tsx":
/*!*****************************************!*\
  !*** ./src/forum/Pages/ProfilePage.tsx ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ProfilePage)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/forum/app */ "flarum/forum/app");
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_app__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_forum_components_UserPage__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/forum/components/UserPage */ "flarum/forum/components/UserPage");
/* harmony import */ var flarum_forum_components_UserPage__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_components_UserPage__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/common/components/LoadingIndicator */ "flarum/common/components/LoadingIndicator");
/* harmony import */ var flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! flarum/common/components/Button */ "flarum/common/components/Button");
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! flarum/common/helpers/humanTime */ "flarum/common/helpers/humanTime");
/* harmony import */ var flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! flarum/common/components/Select */ "flarum/common/components/Select");
/* harmony import */ var flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var flarum_common_components_Placeholder__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! flarum/common/components/Placeholder */ "flarum/common/components/Placeholder");
/* harmony import */ var flarum_common_components_Placeholder__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Placeholder__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _modals_FeedbackModal__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../modals/FeedbackModal */ "./src/forum/modals/FeedbackModal.js");









var ProfilePage = /*#__PURE__*/function (_UserPage) {
  function ProfilePage() {
    var _this;
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _UserPage.call.apply(_UserPage, [this].concat(args)) || this;
    _this.feedbacks = [];
    _this.stats = null;
    _this.loading = false;
    _this.statsLoading = false;
    _this.filter = 'all';
    return _this;
  }
  (0,_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(ProfilePage, _UserPage);
  var _proto = ProfilePage.prototype;
  _proto.oninit = function oninit(vnode) {
    _UserPage.prototype.oninit.call(this, vnode);
    this.loading = true;
    this.statsLoading = true;
    this.loadUser(this.attrs.username);
  };
  _proto.oncreate = function oncreate(vnode) {
    _UserPage.prototype.oncreate.call(this, vnode);
    this.loadFeedbacks();
    this.loadStats();
  };
  _proto.loadFeedbacks = function loadFeedbacks() {
    var _this2 = this;
    this.loading = true;
    flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().request({
      method: 'GET',
      url: flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().forum.attribute('apiUrl') + '/trader/feedback',
      params: {
        filter: {
          user: this.user.id(),
          type: this.filter === 'all' ? null : this.filter
        }
      }
    }).then(function (response) {
      _this2.feedbacks = Array.isArray(response) ? response : response.data || [];
      _this2.loading = false;
      m.redraw();
    })["catch"](function (error) {
      _this2.feedbacks = [];
      _this2.loading = false;
      m.redraw();
    });
  };
  _proto.loadStats = function loadStats() {
    var _this3 = this;
    this.statsLoading = true;
    flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().request({
      method: 'GET',
      url: flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().forum.attribute('apiUrl') + '/trader/stats/' + this.user.id()
    }).then(function (response) {
      if (response && response.data) {
        var data = response.data.attributes || response.data;
        _this3.stats = {
          score: data.score || 0,
          positive_count: data.positive_count || 0,
          neutral_count: data.neutral_count || 0,
          negative_count: data.negative_count || 0
        };
      } else {
        _this3.stats = {
          score: 0,
          positive_count: 0,
          neutral_count: 0,
          negative_count: 0
        };
      }
      _this3.statsLoading = false;
      m.redraw();
    })["catch"](function (error) {
      _this3.stats = {
        score: 0,
        positive_count: 0,
        neutral_count: 0,
        negative_count: 0
      };
      _this3.statsLoading = false;
      m.redraw();
    });
  };
  _proto.content = function content() {
    if (this.loading || !this.user) {
      return m("div", {
        className: "TraderFeedbackPage"
      }, m((flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3___default()), null));
    }
    return m("div", {
      className: "TraderFeedbackPage"
    }, m("div", {
      className: "TraderFeedbackPage-header"
    }, m("h2", null, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.title', {
      username: this.user.displayName()
    })), this.statsSection()), this.filterSection(), this.feedbacksList());
  };
  _proto.statsSection = function statsSection() {
    if (this.statsLoading) {
      return m("div", {
        className: "TraderFeedbackPage-stats"
      }, m((flarum_common_components_LoadingIndicator__WEBPACK_IMPORTED_MODULE_3___default()), {
        size: "small"
      }));
    }
    if (!this.stats) {
      return null;
    }
    var total = this.stats.positive_count + this.stats.neutral_count + this.stats.negative_count;
    var scorePercentage = total > 0 ? Math.round(this.stats.score) : 0;
    return m("div", {
      className: "TraderFeedbackPage-stats"
    }, m("div", {
      className: "TraderFeedbackPage-stat score"
    }, m("div", {
      className: "TraderFeedbackPage-stat-value"
    }, scorePercentage, "%"), m("div", {
      className: "TraderFeedbackPage-stat-label"
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.score'))), m("div", {
      className: "TraderFeedbackPage-stat positive"
    }, m("div", {
      className: "TraderFeedbackPage-stat-value"
    }, this.stats.positive_count), m("div", {
      className: "TraderFeedbackPage-stat-label"
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.positive'))), m("div", {
      className: "TraderFeedbackPage-stat neutral"
    }, m("div", {
      className: "TraderFeedbackPage-stat-value"
    }, this.stats.neutral_count), m("div", {
      className: "TraderFeedbackPage-stat-label"
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.neutral'))), m("div", {
      className: "TraderFeedbackPage-stat negative"
    }, m("div", {
      className: "TraderFeedbackPage-stat-value"
    }, this.stats.negative_count), m("div", {
      className: "TraderFeedbackPage-stat-label"
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.stats.negative'))));
  };
  _proto.filterSection = function filterSection() {
    var _this4 = this;
    return m("div", {
      className: "TraderFeedbackPage-filters"
    }, m((flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_6___default()), {
      value: this.filter,
      options: {
        all: flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.all'),
        positive: flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.positive'),
        neutral: flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.neutral'),
        negative: flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.filter.negative')
      },
      onchange: function onchange(value) {
        _this4.filter = value;
        _this4.loadFeedbacks();
      }
    }), (flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().session).user && flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().session.user.id() !== this.user.id() && m((flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_4___default()), {
      className: "Button Button--primary",
      onclick: function onclick() {
        flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().modal.show(_modals_FeedbackModal__WEBPACK_IMPORTED_MODULE_8__["default"], {
          user: _this4.user
        });
      }
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.give_feedback_button')));
  };
  _proto.feedbacksList = function feedbacksList() {
    var _this5 = this;
    if (!this.feedbacks || !Array.isArray(this.feedbacks) || this.feedbacks.length === 0) {
      return m((flarum_common_components_Placeholder__WEBPACK_IMPORTED_MODULE_7___default()), {
        text: flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.feedback_page.no_feedback')
      });
    }
    return m("div", {
      className: "TraderFeedbackPage-list"
    }, this.feedbacks.map(function (feedback) {
      return _this5.feedbackItem(feedback);
    }));
  };
  _proto.feedbackItem = function feedbackItem(feedback) {
    var _displayUser$displayN;
    // Access data from the original API response structure
    var attrs = feedback.attributes || {};
    var fromUserId = attrs.from_user_id;
    console.log('Feedback object:', feedback);
    console.log('Attributes:', attrs);
    console.log('Looking for user ID:', fromUserId);

    // Get user from store
    var fromUser = fromUserId ? flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().store.getById('users', String(fromUserId)) : null;
    console.log('Found user in store:', fromUser);

    // Fallback to placeholder if user not found
    var displayUser = fromUser || {
      displayName: function displayName() {
        return "User #" + (fromUserId || 'Unknown');
      },
      username: function username() {
        return 'anonymous';
      },
      color: function color() {
        return '#888';
      }
    };

    // Fix for created_at being null
    var feedbackDate = attrs.created_at || new Date().toISOString();
    return m("div", {
      className: "FeedbackItem feedback-" + attrs.type,
      key: feedback.id
    }, m("div", {
      className: "FeedbackItem-header"
    }, m("div", {
      className: "FeedbackItem-user"
    }, m("span", {
      className: "Avatar",
      style: {
        background: displayUser.color ? displayUser.color() : '#888',
        width: '32px',
        height: '32px',
        borderRadius: '50%',
        display: 'inline-flex',
        alignItems: 'center',
        justifyContent: 'center',
        marginRight: '8px',
        color: 'white',
        fontSize: '14px',
        fontWeight: 'bold'
      }
    }, ((_displayUser$displayN = displayUser.displayName()[0]) == null ? void 0 : _displayUser$displayN.toUpperCase()) || '?'), m("span", {
      className: "username"
    }, displayUser.displayName())), m("div", {
      className: "FeedbackItem-date"
    }, feedbackDate ? flarum_common_helpers_humanTime__WEBPACK_IMPORTED_MODULE_5___default()(feedbackDate) : 'Unknown date')), m("div", {
      className: "FeedbackItem-meta"
    }, m("span", {
      className: "FeedbackItem-type feedback-type-" + attrs.type,
      style: {
        padding: '2px 8px',
        borderRadius: '4px',
        fontSize: '0.9em',
        background: attrs.type === 'positive' ? '#4CAF5020' : attrs.type === 'negative' ? '#f4433620' : '#FF980020',
        color: attrs.type === 'positive' ? '#2E7D32' : attrs.type === 'negative' ? '#C62828' : '#E65100'
      }
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans("huseyinfiliz-traderfeedback.forum.form.type_" + attrs.type)), m("span", {
      className: "FeedbackItem-role",
      style: {
        marginLeft: '8px',
        padding: '2px 8px',
        borderRadius: '4px',
        fontSize: '0.9em',
        background: 'rgba(0,0,0,0.1)'
      }
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans("huseyinfiliz-traderfeedback.forum.feedback_item.as_" + attrs.role))), m("div", {
      className: "FeedbackItem-comment",
      style: {
        marginTop: '10px',
        padding: '10px',
        background: 'rgba(0,0,0,0.02)',
        borderRadius: '4px'
      }
    }, attrs.comment || 'No comment provided'));
  };
  return ProfilePage;
}((flarum_forum_components_UserPage__WEBPACK_IMPORTED_MODULE_2___default()));


/***/ }),

/***/ "./src/forum/addUserControls.tsx":
/*!***************************************!*\
  !*** ./src/forum/addUserControls.tsx ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ addUserControls)
/* harmony export */ });
/* harmony import */ var flarum_common_extend__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/common/extend */ "flarum/common/extend");
/* harmony import */ var flarum_common_extend__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_common_extend__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/forum/app */ "flarum/forum/app");
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_app__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/common/components/Button */ "flarum/common/components/Button");
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_forum_utils_UserControls__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/forum/utils/UserControls */ "flarum/forum/utils/UserControls");
/* harmony import */ var flarum_forum_utils_UserControls__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_utils_UserControls__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _modals_FeedbackModal__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./modals/FeedbackModal */ "./src/forum/modals/FeedbackModal.js");




 // Modal'ı import et

function addUserControls() {
  (0,flarum_common_extend__WEBPACK_IMPORTED_MODULE_0__.extend)((flarum_forum_utils_UserControls__WEBPACK_IMPORTED_MODULE_3___default()), 'userControls', function (items, user) {
    // Kendine feedback veremez
    if ((flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().session).user && flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().session.user.id() !== user.id()) {
      items.add('giveFeedback', flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default().component({
        icon: 'fas fa-exchange-alt',
        onclick: function onclick() {
          flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().modal.show(_modals_FeedbackModal__WEBPACK_IMPORTED_MODULE_4__["default"], {
            user: user
          });
        }
      }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_1___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button')), 100);
    }
  });
}

/***/ }),

/***/ "./src/forum/addUserProfilePage.tsx":
/*!******************************************!*\
  !*** ./src/forum/addUserProfilePage.tsx ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ addUserProfilePage)
/* harmony export */ });
/* harmony import */ var flarum_common_extend__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/common/extend */ "flarum/common/extend");
/* harmony import */ var flarum_common_extend__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_common_extend__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var flarum_forum_components_UserPage__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/forum/components/UserPage */ "flarum/forum/components/UserPage");
/* harmony import */ var flarum_forum_components_UserPage__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_components_UserPage__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_common_components_LinkButton__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/common/components/LinkButton */ "flarum/common/components/LinkButton");
/* harmony import */ var flarum_common_components_LinkButton__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_LinkButton__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/forum/app */ "flarum/forum/app");
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_app__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _Pages_ProfilePage__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./Pages/ProfilePage */ "./src/forum/Pages/ProfilePage.tsx");





function addUserProfilePage() {
  (flarum_forum_app__WEBPACK_IMPORTED_MODULE_3___default().routes)['user.feedbacks'] = {
    path: '/u/:username/feedbacks',
    component: _Pages_ProfilePage__WEBPACK_IMPORTED_MODULE_4__["default"]
  };
  (0,flarum_common_extend__WEBPACK_IMPORTED_MODULE_0__.extend)((flarum_forum_components_UserPage__WEBPACK_IMPORTED_MODULE_1___default().prototype), 'navItems', function (items) {
    var _this$user;
    items.add('traderFeedbacksLink', m((flarum_common_components_LinkButton__WEBPACK_IMPORTED_MODULE_2___default()), {
      href: flarum_forum_app__WEBPACK_IMPORTED_MODULE_3___default().route('user.feedbacks', {
        username: (_this$user = this.user) == null ? void 0 : _this$user.slug()
      }),
      name: "feedbacks",
      icon: "fas fa-exchange-alt"
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_3___default().translator.trans('huseyinfiliz-traderfeedback.forum.nav.feedback_link')), 79); // Added closing parenthesis here
  });
}

/***/ }),

/***/ "./src/forum/extend.ts":
/*!*****************************!*\
  !*** ./src/forum/extend.ts ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var flarum_common_extenders__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/common/extenders */ "flarum/common/extenders");
/* harmony import */ var flarum_common_extenders__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_common_extenders__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var flarum_common_models_Forum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/common/models/Forum */ "flarum/common/models/Forum");
/* harmony import */ var flarum_common_models_Forum__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_common_models_Forum__WEBPACK_IMPORTED_MODULE_1__);


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ([new (flarum_common_extenders__WEBPACK_IMPORTED_MODULE_0___default().Model)((flarum_common_models_Forum__WEBPACK_IMPORTED_MODULE_1___default())).attribute('huseyinfilizTraderAdmin').attribute('huseyinfilizTraderUser')]);

/***/ }),

/***/ "./src/forum/index.tsx":
/*!*****************************!*\
  !*** ./src/forum/index.tsx ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   extend: () => (/* reexport safe */ _extend__WEBPACK_IMPORTED_MODULE_4__["default"])
/* harmony export */ });
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/forum/app */ "flarum/forum/app");
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_app__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _addUserProfilePage__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./addUserProfilePage */ "./src/forum/addUserProfilePage.tsx");
/* harmony import */ var _addUserControls__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./addUserControls */ "./src/forum/addUserControls.tsx");
/* harmony import */ var _modals_FeedbackModal__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./modals/FeedbackModal */ "./src/forum/modals/FeedbackModal.js");
/* harmony import */ var _extend__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./extend */ "./src/forum/extend.ts");





flarum_forum_app__WEBPACK_IMPORTED_MODULE_0___default().initializers.add('huseyinfiliz-traderfeedback', function () {
  // Modal'ı global olarak kaydet
  (flarum_forum_app__WEBPACK_IMPORTED_MODULE_0___default().feedbackModal) = _modals_FeedbackModal__WEBPACK_IMPORTED_MODULE_3__["default"];

  // Sayfaları ve kontrolleri ekle
  (0,_addUserProfilePage__WEBPACK_IMPORTED_MODULE_1__["default"])();
  (0,_addUserControls__WEBPACK_IMPORTED_MODULE_2__["default"])();
});

/***/ }),

/***/ "./src/forum/modals/FeedbackModal.js":
/*!*******************************************!*\
  !*** ./src/forum/modals/FeedbackModal.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FeedbackModal)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_common_components_Modal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/common/components/Modal */ "flarum/common/components/Modal");
/* harmony import */ var flarum_common_components_Modal__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Modal__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/common/components/Button */ "flarum/common/components/Button");
/* harmony import */ var flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/common/components/Select */ "flarum/common/components/Select");
/* harmony import */ var flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! flarum/common/utils/Stream */ "flarum/common/utils/Stream");
/* harmony import */ var flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! flarum/forum/app */ "flarum/forum/app");
/* harmony import */ var flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(flarum_forum_app__WEBPACK_IMPORTED_MODULE_5__);






var FeedbackModal = /*#__PURE__*/function (_Modal) {
  function FeedbackModal() {
    return _Modal.apply(this, arguments) || this;
  }
  (0,_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(FeedbackModal, _Modal);
  var _proto = FeedbackModal.prototype;
  _proto.oninit = function oninit(vnode) {
    _Modal.prototype.oninit.call(this, vnode);
    this.user = this.attrs.user;
    this.loading = false;
    this.type = flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_4___default()('positive');
    this.role = flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_4___default()('buyer');
    this.comment = flarum_common_utils_Stream__WEBPACK_IMPORTED_MODULE_4___default()('');
  };
  _proto.className = function className() {
    return 'Modal Modal--medium';
  };
  _proto.title = function title() {
    return flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.title', {
      username: this.user.displayName()
    });
  };
  _proto.content = function content() {
    var _this = this;
    return m('.Modal-body', [m('.Form', [m('.Form-group', [m('label', flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.type_label')), flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3___default().component({
      value: this.type(),
      options: {
        'positive': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.type_positive'),
        'neutral': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.type_neutral'),
        'negative': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.type_negative')
      },
      onchange: this.type
    })]), m('.Form-group', [m('label', flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.role_label')), flarum_common_components_Select__WEBPACK_IMPORTED_MODULE_3___default().component({
      value: this.role(),
      options: {
        'buyer': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.role_buyer'),
        'seller': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.role_seller'),
        'trader': flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.role_trader')
      },
      onchange: this.role
    })]), m('.Form-group', [m('label', flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_label')), m('textarea.FormControl', {
      value: this.comment(),
      oninput: function oninput(e) {
        return _this.comment(e.target.value);
      },
      placeholder: flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.comment_placeholder'),
      rows: 5
    })]), m('.Form-group', [flarum_common_components_Button__WEBPACK_IMPORTED_MODULE_2___default().component({
      type: 'submit',
      className: 'Button Button--primary',
      loading: this.loading,
      disabled: !this.comment().trim()
    }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.submit_button'))])])]);
  };
  _proto.onsubmit = function onsubmit(e) {
    var _this2 = this;
    e.preventDefault();
    if (!this.comment().trim()) return;
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
    m.redraw();
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
            comment: this.comment()
          }
        }
      }
    }).then(function () {
      flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().alerts.show({
        type: 'success'
      }, flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().translator.trans('huseyinfiliz-traderfeedback.forum.form.success'));
      _this2.hide();
      window.location.reload();
    })["catch"](function (error) {
      _this2.loading = false;
      m.redraw();
      if (error.response && error.response.errors) {
        flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().alerts.show({
          type: 'error'
        }, error.response.errors[0].detail);
      } else {
        flarum_forum_app__WEBPACK_IMPORTED_MODULE_5___default().alerts.show({
          type: 'error'
        }, 'Error submitting feedback');
      }
    });
  };
  return FeedbackModal;
}((flarum_common_components_Modal__WEBPACK_IMPORTED_MODULE_1___default()));


/***/ }),

/***/ "flarum/common/components/Button":
/*!*****************************************************************!*\
  !*** external "flarum.core.compat['common/components/Button']" ***!
  \*****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/Button'];

/***/ }),

/***/ "flarum/common/components/LinkButton":
/*!*********************************************************************!*\
  !*** external "flarum.core.compat['common/components/LinkButton']" ***!
  \*********************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/LinkButton'];

/***/ }),

/***/ "flarum/common/components/LoadingIndicator":
/*!***************************************************************************!*\
  !*** external "flarum.core.compat['common/components/LoadingIndicator']" ***!
  \***************************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/LoadingIndicator'];

/***/ }),

/***/ "flarum/common/components/Modal":
/*!****************************************************************!*\
  !*** external "flarum.core.compat['common/components/Modal']" ***!
  \****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/Modal'];

/***/ }),

/***/ "flarum/common/components/Placeholder":
/*!**********************************************************************!*\
  !*** external "flarum.core.compat['common/components/Placeholder']" ***!
  \**********************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/Placeholder'];

/***/ }),

/***/ "flarum/common/components/Select":
/*!*****************************************************************!*\
  !*** external "flarum.core.compat['common/components/Select']" ***!
  \*****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/components/Select'];

/***/ }),

/***/ "flarum/common/extend":
/*!******************************************************!*\
  !*** external "flarum.core.compat['common/extend']" ***!
  \******************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/extend'];

/***/ }),

/***/ "flarum/common/extenders":
/*!*********************************************************!*\
  !*** external "flarum.core.compat['common/extenders']" ***!
  \*********************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/extenders'];

/***/ }),

/***/ "flarum/common/helpers/humanTime":
/*!*****************************************************************!*\
  !*** external "flarum.core.compat['common/helpers/humanTime']" ***!
  \*****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/helpers/humanTime'];

/***/ }),

/***/ "flarum/common/models/Forum":
/*!************************************************************!*\
  !*** external "flarum.core.compat['common/models/Forum']" ***!
  \************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/models/Forum'];

/***/ }),

/***/ "flarum/common/utils/Stream":
/*!************************************************************!*\
  !*** external "flarum.core.compat['common/utils/Stream']" ***!
  \************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['common/utils/Stream'];

/***/ }),

/***/ "flarum/forum/app":
/*!**************************************************!*\
  !*** external "flarum.core.compat['forum/app']" ***!
  \**************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['forum/app'];

/***/ }),

/***/ "flarum/forum/components/UserPage":
/*!******************************************************************!*\
  !*** external "flarum.core.compat['forum/components/UserPage']" ***!
  \******************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['forum/components/UserPage'];

/***/ }),

/***/ "flarum/forum/utils/UserControls":
/*!*****************************************************************!*\
  !*** external "flarum.core.compat['forum/utils/UserControls']" ***!
  \*****************************************************************/
/***/ ((module) => {

"use strict";
module.exports = flarum.core.compat['forum/utils/UserControls'];

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
  !*** ./forum.ts ***!
  \******************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   extend: () => (/* reexport safe */ _src_forum__WEBPACK_IMPORTED_MODULE_0__.extend)
/* harmony export */ });
/* harmony import */ var _src_forum__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./src/forum */ "./src/forum/index.tsx");

})();

module.exports = __webpack_exports__;
/******/ })()
;
//# sourceMappingURL=forum.js.map