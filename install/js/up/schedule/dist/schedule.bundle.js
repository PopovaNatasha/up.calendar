this.BX = this.BX || {};
this.BX.Up = this.BX.Up || {};
(function (exports,main_core) {
	'use strict';

	var Schedule = /*#__PURE__*/function () {
	  function Schedule() {
	    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
	      name: 'Schedule'
	    };
	    babelHelpers.classCallCheck(this, Schedule);
	    this.name = options.name;
	  }
	  babelHelpers.createClass(Schedule, [{
	    key: "setName",
	    value: function setName(name) {
	      if (main_core.Type.isString(name)) {
	        this.name = name;
	      }
	    }
	  }, {
	    key: "getName",
	    value: function getName() {
	      return this.name;
	    }
	  }]);
	  return Schedule;
	}();

	exports.Schedule = Schedule;

}((this.BX.Up.Calendar = this.BX.Up.Calendar || {}),BX));
