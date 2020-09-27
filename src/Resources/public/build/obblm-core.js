(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["obblm-core"],{

/***/ "./assets/js/app.js":
/*!**************************!*\
  !*** ./assets/js/app.js ***!
  \**************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _navigation__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./navigation */ "./assets/js/navigation.js");
/* harmony import */ var _navigation__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_navigation__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _collections__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./collections */ "./assets/js/collections.js");
/* harmony import */ var _collections__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_collections__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _obblm_component_form__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./obblm/component/form */ "./assets/js/obblm/component/form.js");
/* harmony import */ var _obblm_component_form__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_obblm_component_form__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _obblm_component_message__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./obblm/component/message */ "./assets/js/obblm/component/message.js");
/* harmony import */ var _obblm_component_message__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_obblm_component_message__WEBPACK_IMPORTED_MODULE_3__);
//Application JS





/***/ }),

/***/ "./assets/js/collections.js":
/*!**********************************!*\
  !*** ./assets/js/collections.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery, $) {__webpack_require__(/*! core-js/modules/es.regexp.exec */ "./node_modules/core-js/modules/es.regexp.exec.js");

__webpack_require__(/*! core-js/modules/es.string.replace */ "./node_modules/core-js/modules/es.string.replace.js");

jQuery(document).ready(function () {
  var $collections = $('.attach-collection-actions');
  $collections.each(function (i, el) {
    $(el).delegate('.add-another-collection-widget', "click", function (e) {
      e.preventDefault();
      var list = jQuery(jQuery(this).attr('data-list-selector'), el); // Try to find the counter of the list or use the length of the list

      var counter = $(list).data('widget-counter') || list.children().length; // grab the prototype template

      var newWidget = $(list).attr('data-prototype'); // replace the "__name__" used in the id and name of the prototype
      // with a number that's unique to your emails
      // end name attribute looks like name="contact[emails][2]"

      newWidget = newWidget.replace(/__name__/g, counter); // Increase the counter

      counter++; // And store it, the length cannot be used if deleting widgets is allowed

      $(list).data('widget-counter', counter); // create a new list element and add it to the list

      jQuery(newWidget).appendTo(list);
      return false;
    });
    $(el).delegate('.remove-collection-widget', "click", function (e) {
      // Try to find the counter of the list or use the length of the list
      $(this).closest(jQuery(this).attr('data-item-selector')).remove();
      e.preventDefault();
      return false;
    });
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js"), __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./assets/js/navigation.js":
/*!*********************************!*\
  !*** ./assets/js/navigation.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function($) {$(document).ready(function () {
  var header = $('#header');
  var body = $('body.pushable');
  var overlay = $('<div class="overlay" />');
  $('.launch', header).click(function (event) {
    $(body).toggleClass('open');
  });
  $(body).append(overlay);
  $(overlay).click(function (event) {
    $(body).toggleClass('open');
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./assets/js/obblm/component/form.js":
/*!*******************************************!*\
  !*** ./assets/js/obblm/component/form.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function($) {$(document).ready(function () {
  $forms = $('.form');
  $forms.each(function (i, form) {
    $(form).delegate('.field', "blur", function (e) {
      $('.field', form).removeClass('focused'); //$(e.currentTarget).addClass('focused');
    });
    $(form).delegate('.field', "focusin", function (e) {
      $('.field', form).removeClass('focused');
      $(e.currentTarget).addClass('focused');
    });
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./assets/js/obblm/component/message.js":
/*!**********************************************!*\
  !*** ./assets/js/obblm/component/message.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function($) {$(document).ready(function () {
  $closables = $('.message.closable');
  $closables.each(function (i, el) {
    $(el).prepend($('<a class="closer close"></a>'));
    $('.close', el).click(function () {
      $(el).hide('fade');
    });
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ })

},[["./assets/js/app.js","runtime","vendors~obblm-core"]]]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvYXBwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qcy9jb2xsZWN0aW9ucy5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvbmF2aWdhdGlvbi5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvb2JibG0vY29tcG9uZW50L2Zvcm0uanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzL29iYmxtL2NvbXBvbmVudC9tZXNzYWdlLmpzIl0sIm5hbWVzIjpbImpRdWVyeSIsImRvY3VtZW50IiwicmVhZHkiLCIkY29sbGVjdGlvbnMiLCIkIiwiZWFjaCIsImkiLCJlbCIsImRlbGVnYXRlIiwiZSIsInByZXZlbnREZWZhdWx0IiwibGlzdCIsImF0dHIiLCJjb3VudGVyIiwiZGF0YSIsImNoaWxkcmVuIiwibGVuZ3RoIiwibmV3V2lkZ2V0IiwicmVwbGFjZSIsImFwcGVuZFRvIiwiY2xvc2VzdCIsInJlbW92ZSIsImhlYWRlciIsImJvZHkiLCJvdmVybGF5IiwiY2xpY2siLCJldmVudCIsInRvZ2dsZUNsYXNzIiwiYXBwZW5kIiwiJGZvcm1zIiwiZm9ybSIsInJlbW92ZUNsYXNzIiwiY3VycmVudFRhcmdldCIsImFkZENsYXNzIiwiJGNsb3NhYmxlcyIsInByZXBlbmQiLCJoaWRlIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7O0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7QUNIQUEsTUFBTSxDQUFDQyxRQUFELENBQU4sQ0FBaUJDLEtBQWpCLENBQXVCLFlBQVk7QUFDL0IsTUFBSUMsWUFBWSxHQUFHQyxDQUFDLENBQUMsNEJBQUQsQ0FBcEI7QUFDQUQsY0FBWSxDQUFDRSxJQUFiLENBQWtCLFVBQVVDLENBQVYsRUFBYUMsRUFBYixFQUFpQjtBQUMvQkgsS0FBQyxDQUFFRyxFQUFGLENBQUQsQ0FBUUMsUUFBUixDQUFrQixnQ0FB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UUFBUixDQUFpQixRQUFqQixFQUEyQixTQUEzQixFQUFzQyxVQUFTQyxDQUFULEVBQVk7QUFDOUNMLE9BQUMsQ0FBQyxRQUFELEVBQVcwQixJQUFYLENBQUQsQ0FBa0JDLFdBQWxCLENBQThCLFNBQTlCO0FBQ0EzQixPQUFDLENBQUNLLENBQUMsQ0FBQ3VCLGFBQUgsQ0FBRCxDQUFtQkMsUUFBbkIsQ0FBNEIsU0FBNUI7QUFDSCxLQUhEO0FBSUgsR0FURDtBQVVILENBYkQsRTs7Ozs7Ozs7Ozs7O0FDQUE3QiwwQ0FBQyxDQUFDSCxRQUFELENBQUQsQ0FBWUMsS0FBWixDQUFrQixZQUFZO0FBQzFCZ0MsWUFBVSxHQUFHOUIsQ0FBQyxDQUFDLG1CQUFELENBQWQ7QUFDQThCLFlBQVUsQ0FBQzdCLElBQVgsQ0FBZ0IsVUFBVUMsQ0FBVixFQUFZQyxFQUFaLEVBQWdCO0FBQzVCSCxLQUFDLENBQUNHLEVBQUQsQ0FBRCxDQUFNNEIsT0FBTixDQUFjL0IsQ0FBQyxDQUFDLDhCQUFELENBQWY7QUFDQUEsS0FBQyxDQUFDLFFBQUQsRUFBV0csRUFBWCxDQUFELENBQWdCa0IsS0FBaEIsQ0FBc0IsWUFBVztBQUM5QnJCLE9BQUMsQ0FBQ0csRUFBRCxDQUFELENBQU02QixJQUFOLENBQVcsTUFBWDtBQUNGLEtBRkQ7QUFHSCxHQUxEO0FBTUgsQ0FSRCxFIiwiZmlsZSI6Im9iYmxtLWNvcmUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvL0FwcGxpY2F0aW9uIEpTXG5pbXBvcnQnLi9uYXZpZ2F0aW9uJztcbmltcG9ydCAnLi9jb2xsZWN0aW9ucyc7XG5pbXBvcnQgJy4vb2JibG0vY29tcG9uZW50L2Zvcm0nO1xuaW1wb3J0ICcuL29iYmxtL2NvbXBvbmVudC9tZXNzYWdlJztcbiIsImpRdWVyeShkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xuICAgIHZhciAkY29sbGVjdGlvbnMgPSAkKCcuYXR0YWNoLWNvbGxlY3Rpb24tYWN0aW9ucycpO1xuICAgICRjb2xsZWN0aW9ucy5lYWNoKGZ1bmN0aW9uIChpLCBlbCkge1xuICAgICAgICAkKCBlbCApLmRlbGVnYXRlKCAnLmFkZC1hbm90aGVyLWNvbGxlY3Rpb24td2lkZ2V0JywgXCJjbGlja1wiLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgdmFyIGxpc3QgPSBqUXVlcnkoalF1ZXJ5KHRoaXMpLmF0dHIoJ2RhdGEtbGlzdC1zZWxlY3RvcicpLCBlbCk7XG4gICAgICAgICAgICAvLyBUcnkgdG8gZmluZCB0aGUgY291bnRlciBvZiB0aGUgbGlzdCBvciB1c2UgdGhlIGxlbmd0aCBvZiB0aGUgbGlzdFxuICAgICAgICAgICAgdmFyIGNvdW50ZXIgPSAkKGxpc3QpLmRhdGEoJ3dpZGdldC1jb3VudGVyJykgfHwgbGlzdC5jaGlsZHJlbigpLmxlbmd0aDtcblxuICAgICAgICAgICAgLy8gZ3JhYiB0aGUgcHJvdG90eXBlIHRlbXBsYXRlXG4gICAgICAgICAgICB2YXIgbmV3V2lkZ2V0ID0gJChsaXN0KS5hdHRyKCdkYXRhLXByb3RvdHlwZScpO1xuICAgICAgICAgICAgLy8gcmVwbGFjZSB0aGUgXCJfX25hbWVfX1wiIHVzZWQgaW4gdGhlIGlkIGFuZCBuYW1lIG9mIHRoZSBwcm90b3R5cGVcbiAgICAgICAgICAgIC8vIHdpdGggYSBudW1iZXIgdGhhdCdzIHVuaXF1ZSB0byB5b3VyIGVtYWlsc1xuICAgICAgICAgICAgLy8gZW5kIG5hbWUgYXR0cmlidXRlIGxvb2tzIGxpa2UgbmFtZT1cImNvbnRhY3RbZW1haWxzXVsyXVwiXG4gICAgICAgICAgICBuZXdXaWRnZXQgPSBuZXdXaWRnZXQucmVwbGFjZSgvX19uYW1lX18vZywgY291bnRlcik7XG4gICAgICAgICAgICAvLyBJbmNyZWFzZSB0aGUgY291bnRlclxuICAgICAgICAgICAgY291bnRlcisrO1xuICAgICAgICAgICAgLy8gQW5kIHN0b3JlIGl0LCB0aGUgbGVuZ3RoIGNhbm5vdCBiZSB1c2VkIGlmIGRlbGV0aW5nIHdpZGdldHMgaXMgYWxsb3dlZFxuICAgICAgICAgICAgJChsaXN0KS5kYXRhKCd3aWRnZXQtY291bnRlcicsIGNvdW50ZXIpO1xuXG4gICAgICAgICAgICAvLyBjcmVhdGUgYSBuZXcgbGlzdCBlbGVtZW50IGFuZCBhZGQgaXQgdG8gdGhlIGxpc3RcbiAgICAgICAgICAgIGpRdWVyeShuZXdXaWRnZXQpLmFwcGVuZFRvKGxpc3QpO1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9KTtcbiAgICAgICAgJCggZWwgKS5kZWxlZ2F0ZSggJy5yZW1vdmUtY29sbGVjdGlvbi13aWRnZXQnLCBcImNsaWNrXCIsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAvLyBUcnkgdG8gZmluZCB0aGUgY291bnRlciBvZiB0aGUgbGlzdCBvciB1c2UgdGhlIGxlbmd0aCBvZiB0aGUgbGlzdFxuICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KGpRdWVyeSh0aGlzKS5hdHRyKCdkYXRhLWl0ZW0tc2VsZWN0b3InKSlcbiAgICAgICAgICAgICAgICAucmVtb3ZlKCk7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH0pO1xuICAgIH0pO1xufSk7IiwiJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xuICAgIHZhciBoZWFkZXIgPSAkKCcjaGVhZGVyJyk7XG4gICAgdmFyIGJvZHkgPSAkKCdib2R5LnB1c2hhYmxlJyk7XG4gICAgdmFyIG92ZXJsYXkgPSAkKCc8ZGl2IGNsYXNzPVwib3ZlcmxheVwiIC8+Jyk7XG5cbiAgICAkKCcubGF1bmNoJywgaGVhZGVyKS5jbGljayhmdW5jdGlvbihldmVudCkge1xuICAgICAgICAkKGJvZHkpLnRvZ2dsZUNsYXNzKCdvcGVuJyk7XG4gICAgfSk7XG4gICAgJChib2R5KS5hcHBlbmQob3ZlcmxheSlcbiAgICAkKG92ZXJsYXkpLmNsaWNrKGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICQoYm9keSkudG9nZ2xlQ2xhc3MoJ29wZW4nKTtcbiAgICB9KTtcbn0pOyIsIiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uICgpIHtcbiAgICAkZm9ybXMgPSAkKCcuZm9ybScpO1xuXG4gICAgJGZvcm1zLmVhY2goZnVuY3Rpb24gKGksIGZvcm0pIHtcbiAgICAgICAgJChmb3JtKS5kZWxlZ2F0ZSgnLmZpZWxkJywgXCJibHVyXCIsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICQoJy5maWVsZCcsIGZvcm0pLnJlbW92ZUNsYXNzKCdmb2N1c2VkJyk7XG4gICAgICAgICAgICAvLyQoZS5jdXJyZW50VGFyZ2V0KS5hZGRDbGFzcygnZm9jdXNlZCcpO1xuICAgICAgICB9KTtcbiAgICAgICAgJChmb3JtKS5kZWxlZ2F0ZSgnLmZpZWxkJywgXCJmb2N1c2luXCIsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICQoJy5maWVsZCcsIGZvcm0pLnJlbW92ZUNsYXNzKCdmb2N1c2VkJyk7XG4gICAgICAgICAgICAkKGUuY3VycmVudFRhcmdldCkuYWRkQ2xhc3MoJ2ZvY3VzZWQnKTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG59KTsiLCIkKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbiAoKSB7XG4gICAgJGNsb3NhYmxlcyA9ICQoJy5tZXNzYWdlLmNsb3NhYmxlJyk7XG4gICAgJGNsb3NhYmxlcy5lYWNoKGZ1bmN0aW9uIChpLGVsKSB7XG4gICAgICAgICQoZWwpLnByZXBlbmQoJCgnPGEgY2xhc3M9XCJjbG9zZXIgY2xvc2VcIj48L2E+JykpO1xuICAgICAgICAkKCcuY2xvc2UnLCBlbCkuY2xpY2soZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICQoZWwpLmhpZGUoJ2ZhZGUnKTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG59KTsiXSwic291cmNlUm9vdCI6IiJ9