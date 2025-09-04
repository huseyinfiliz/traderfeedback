/******/ (() => { // webpackBootstrap
/******/ 	// runtime can't be in strict mode because a global variable is assign and maybe created.
/******/ 	var __webpack_modules__ = ({

/***/ "./src/admin/index.js":
/*!****************************!*\
  !*** ./src/admin/index.js ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/admin/app */ "flarum/admin/app");
/* harmony import */ var flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_admin_app__WEBPACK_IMPORTED_MODULE_0__);

flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().initializers.add('huseyinfiliz-traderfeedback', function () {
  flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().extensionData["for"]('huseyinfiliz-traderfeedback').registerSetting({
    setting: 'huseyinfiliz.traderfeedback.allowNegative',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.allow_negative_label'),
    type: 'boolean'
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.requireApproval',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.require_approval_label'),
    type: 'boolean'
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.minDays',
    label: 'Minimum membership days required',
    help: 'Users must be a member for this many days before giving feedback (0 = disabled)',
    type: 'number',
    min: 0
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.minPosts',
    label: 'Minimum posts required',
    help: 'Users must have this many posts before giving feedback (0 = disabled)',
    type: 'number',
    min: 0
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.requireDiscussion',
    label: 'Require discussion link',
    help: 'Feedback must be linked to a discussion',
    type: 'boolean'
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.minLength',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.min_length_label'),
    type: 'number',
    min: 1
  }).registerSetting({
    setting: 'huseyinfiliz.traderfeedback.maxLength',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.settings.max_length_label'),
    type: 'number',
    min: 1
  }).registerPermission({
    icon: 'fas fa-exchange-alt',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.permissions.give_feedback'),
    permission: 'huseyinfiliz-traderfeedback.giveFeedback'
  }, 'reply').registerPermission({
    icon: 'fas fa-exchange-alt',
    label: flarum_admin_app__WEBPACK_IMPORTED_MODULE_0___default().translator.trans('huseyinfiliz-traderfeedback.admin.permissions.moderate_feedback'),
    permission: 'huseyinfiliz-traderfeedback.moderateFeedback'
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