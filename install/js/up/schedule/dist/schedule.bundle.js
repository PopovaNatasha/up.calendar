this.BX = this.BX || {};
this.BX.Up = this.BX.Up || {};
(function (exports,main_core) {
	'use strict';

	var Schedule = /*#__PURE__*/function () {
	  function Schedule() {
	    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
	    babelHelpers.classCallCheck(this, Schedule);
	    this.idTeam = options.idTeam;
	    this.rootNodeId = options.rootNodeId;
	    this.rootNode = document.getElementById(this.rootNodeId);
	    this.singleEventsList = [];
	    this.regularEventsList = [];
	    this.calendar = this.createCalendar();
	    this.reload();
	  }
	  babelHelpers.createClass(Schedule, [{
	    key: "reload",
	    value: function reload() {
	      var _this = this;
	      this.loadEventsList(this.idTeam).then(function (eventsList) {
	        _this.singleEventsList = eventsList['singleEvents'];
	        _this.regularEventsList = eventsList['regularEvents'];
	        _this.addEvents();
	        _this.addRegularEvents();
	      });
	    }
	  }, {
	    key: "loadEventsList",
	    value: function loadEventsList(idTeam) {
	      return new Promise(function (resolve, reject) {
	        BX.ajax.runAction('up:calendar.calendar.getEventsList', {
	          data: {
	            idTeam: idTeam
	          }
	        }).then(function (response) {
	          var eventsList = response.data.events;
	          console.log(eventsList);
	          resolve(eventsList);
	        })["catch"](function (error) {
	          reject(error);
	        });
	      });
	    }
	  }, {
	    key: "loadRegularEventsList",
	    value: function loadRegularEventsList(idTeam) {
	      return new Promise(function (resolve, reject) {
	        BX.ajax.runAction('up:calendar.calendar.getRegularEventsList', {
	          data: {
	            idTeam: idTeam
	          }
	        }).then(function (response) {
	          var eventsList = response.data.events;
	          resolve(eventsList);
	        })["catch"](function (error) {
	          reject(error);
	        });
	      });
	    }
	  }, {
	    key: "createCalendar",
	    value: function createCalendar() {
	      return new tui.Calendar("#".concat(this.rootNodeId), {
	        // isReadOnly: false,
	        showSlidebar: true,
	        showMenu: true,
	        useFormPopup: false,
	        useDetailPopup: false,
	        useCreationPopup: false,
	        defaultView: 'month',
	        taskView: true,
	        scheduleView: false,
	        template: {
	          milestone: function milestone(schedule) {
	            return '<span style="color:red;"><i class="fa fa-flag"></i> ' + schedule.title + '</span>';
	          },
	          milestoneTitle: function milestoneTitle() {
	            return 'Milestone';
	          },
	          task: function task(schedule) {
	            return '&nbsp;&nbsp;#' + schedule.title;
	          },
	          taskTitle: function taskTitle() {
	            return '<label><input type="checkbox" />Task</label>';
	          },
	          allday: function allday(schedule) {
	            return schedule.title + ' <i class="fa fa-refresh"></i>';
	          },
	          alldayTitle: function alldayTitle() {
	            return 'All Day';
	          },
	          time: function time(schedule) {
	            return '<strong>' + moment(schedule.start.getTime()).format('HH:mm') + '</strong> ' + schedule.title;
	          },
	          timegridDisplayPrimaryTime: function timegridDisplayPrimaryTime(time) {
	            return time.time.d.getHours() + ':00';
	          }
	        },
	        week: {
	          dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
	          startDayOfWeek: 1,
	          narrowWeekend: false,
	          taskView: false,
	          eventView: ['time'],
	          hourStart: 6,
	          hourEnd: 23
	        },
	        month: {
	          dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
	          startDayOfWeek: 1,
	          narrowWeekend: false
	        },
	        // list of Calendars that can be used to add new schedule
	        calendars: this.getIdCalendars()
	      });
	    }
	  }, {
	    key: "addEvents",
	    value: function addEvents() {
	      var eventsList = this.singleEventsList;
	      var calendar = this.calendar;
	      eventsList.forEach(function (event) {
	        var dayTimeStart = event['DATE_TIME_FROM'].split('+');
	        var dayTimeEnd = event['DATE_TIME_TO'].split('+');
	        calendar.createEvents([{
	          id: event['ID'],
	          calendarId: event['ID_TEAM'],
	          title: event['TITLE'],
	          start: dayTimeStart[0],
	          end: dayTimeEnd[0],
	          category: 'time'
	        }]);
	      });
	      console.log(eventsList);
	    }
	  }, {
	    key: "addRegularEvents",
	    value: function addRegularEvents() {
	      var eventsList = this.regularEventsList;
	      var calendar = this.calendar;
	      var repeatUntil = '2023-12-31';
	      eventsList.forEach(function (event) {
	        var dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
	        var dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
	        var dayStep = Number(event['DAY_STEP']);
	        while (moment(dayTimeStart).isBefore(repeatUntil)) {
	          calendar.createEvents([{
	            id: event['ID'],
	            calendarId: 'team',
	            title: event['TITLE'],
	            start: dayTimeStart,
	            end: dayTimeEnd,
	            category: 'time'
	          }]);
	          dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
	          dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
	        }
	      });
	    }
	  }, {
	    key: "getIdCalendars",
	    value: function getIdCalendars() {
	      var idTeams = this.idTeam;
	      var calendarId = [];
	      idTeams.forEach(function (idTeam) {
	        calendarId.push({
	          id: idTeam
	        });
	      });
	      return calendarId;
	    }
	  }]);
	  return Schedule;
	}();

	exports.Schedule = Schedule;

}((this.BX.Up.Calendar = this.BX.Up.Calendar || {}),BX));
