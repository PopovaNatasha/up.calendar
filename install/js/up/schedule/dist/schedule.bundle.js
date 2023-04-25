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
	    this.isUser = options.isUser;
	    this.singleEventsList = [];
	    this.regularEventsList = [];
	    this.userStoryEvents = [];
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
	        _this.userStoryEvents = eventsList['userStoryEvents'];
	        if (_this.isUser) {
	          _this.filter();
	          _this.addEventsForUser();
	          _this.addRegularEventsForUser();
	        } else {
	          _this.addEvents();
	          _this.addRegularEvents();
	        }
	        // this.setCalendarColor()
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
	    key: "createCalendar",
	    value: function createCalendar() {
	      return new tui.Calendar("#".concat(this.rootNodeId), {
	        // isReadOnly: false,
	        // showSlidebar: true,
	        // showMenu: true,
	        useFormPopup: false,
	        useDetailPopup: false,
	        useCreationPopup: false,
	        defaultView: 'month',
	        taskView: true,
	        scheduleView: false,
	        template: {
	          task: function task(schedule) {
	            return '&nbsp;&nbsp;#' + schedule.title;
	          },
	          taskTitle: function taskTitle() {
	            return '<label><input type="checkbox" />Task</label>';
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
	        calendars: [{
	          id: 'story',
	          name: 'Прошедшие события',
	          color: '#e8e8e8',
	          backgroundColor: '#585858',
	          borderColor: '#a1b56c',
	          dragBackgroundColor: '#585858'
	        }]
	      });
	    }
	  }, {
	    key: "addEvents",
	    value: function addEvents() {
	      var eventsList = this.singleEventsList;
	      var calendar = this.calendar;
	      eventsList.forEach(function (event) {
	        var dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
	        var dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
	        calendar.createEvents([{
	          id: event['ID'],
	          calendarId: event['ID_TEAM'],
	          title: event['TITLE'],
	          start: dayTimeStart,
	          end: dayTimeEnd,
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
	            calendarId: event['ID_TEAM'],
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
	    key: "addEventsForUser",
	    value: function addEventsForUser() {
	      var eventsList = this.singleEventsList;
	      var storyEventList = this.userStoryEvents;
	      var calendar = this.calendar;
	      eventsList.forEach(function (event) {
	        var dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
	        var dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
	        var nowDay = moment().format('YYYY-MM-DD');
	        var dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
	        if (moment(nowDay).isBefore(dayStart)) {
	          calendar.createEvents([{
	            id: event['ID'],
	            calendarId: event['ID_TEAM'],
	            title: event['TITLE'],
	            start: dayTimeStart,
	            end: dayTimeEnd,
	            category: 'time'
	          }]);
	        }
	      });
	      storyEventList.forEach(function (event) {
	        var dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
	        var dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
	        if (!event['DAY_STEP']) {
	          calendar.createEvents([{
	            id: event['ID'],
	            calendarId: 'story',
	            title: event['TITLE_EVENT'],
	            start: dayTimeStart,
	            end: dayTimeEnd,
	            category: 'time'
	          }]);
	        }
	      });
	    }
	  }, {
	    key: "addRegularEventsForUser",
	    value: function addRegularEventsForUser() {
	      var eventsList = this.regularEventsList;
	      var storyEventList = this.userStoryEvents;
	      var calendar = this.calendar;
	      var repeatUntil = '2023-12-31';
	      eventsList.forEach(function (event) {
	        var dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
	        var dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
	        var dayStep = Number(event['DAY_STEP']);
	        while (moment(dayTimeStart).isBefore(repeatUntil)) {
	          var nowDay = moment().format('YYYY-MM-DD');
	          var dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
	          if (moment(nowDay).isBefore(dayStart)) {
	            calendar.createEvents([{
	              id: event['ID'],
	              calendarId: event['ID_TEAM'],
	              title: event['TITLE'],
	              start: dayTimeStart,
	              end: dayTimeEnd,
	              category: 'time'
	            }]);
	          }
	          dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
	          dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
	        }
	      });
	      storyEventList.forEach(function (event) {
	        if (event['DAY_STEP']) {
	          var dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
	          var dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
	          var dayStep = Number(event['DAY_STEP']);
	          var nowDay = moment().format('YYYY-MM-DD');
	          while (moment(dayTimeStart).isBefore(nowDay)) {
	            calendar.createEvents([{
	              id: event['ID'],
	              calendarId: 'story',
	              title: event['TITLE_EVENT'],
	              start: dayTimeStart,
	              end: dayTimeEnd,
	              category: 'time'
	            }]);
	            dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
	            dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
	          }
	        }
	      });
	    }
	  }, {
	    key: "filter",
	    value: function filter() {
	      var idCalendars = this.idTeam;
	      var sidebar = document.querySelector('.sidebar');
	      console.log(sidebar);
	      var calendar = this.calendar;
	      sidebar.addEventListener('click', function (e) {
	        if ('value' in e.target) {
	          // if (e.target.value === 'all') {
	          // 	if (appState.activeCalendarIds.length > 0) {
	          // 		cal.setCalendarVisibility(appState.activeCalendarIds, false);
	          // 		appState.activeCalendarIds = [];
	          // 		setAllCheckboxes(false);
	          // 	} else {
	          // 		appState.activeCalendarIds = MOCK_CALENDARS.map(function (calendar) {
	          // 			return calendar.id;
	          // 		});
	          // 		cal.setCalendarVisibility(appState.activeCalendarIds, true);
	          // 		setAllCheckboxes(true);
	          // 	}
	          // } else
	          if (idCalendars.indexOf(e.target.value) > -1) {
	            idCalendars.splice(idCalendars.indexOf(e.target.value), 1);
	            calendar.setCalendarVisibility(e.target.value, false);
	            // setCheckboxBackgroundColor(e.target);
	          } else {
	            idCalendars.push(e.target.value);
	            calendar.setCalendarVisibility(e.target.value, true);
	            // setCheckboxBackgroundColor(e.target);
	          }
	        }
	      });
	    } // setCalendarColor()
	    // {
	    // 	let idTeams = this.idTeam;
	    // 	let calendar = this.calendar;
	    // 	idTeams.forEach(idTeam => {
	    // 		let color = this.generateColor();
	    // 		calendar.setCalendarColor(idTeam, {
	    // 			color: '#e8e8e8',
	    // 			backgroundColor: color,
	    // 			borderColor: color,
	    // 			dragBackgroundColor: color,
	    // 		});
	    // 	});
	    // 	console.log((Number('24')).toString(16))
	    // }
	    //
	    // generateColor()
	    // {
	    // 	return '#' + Math.floor(Math.random()*16777215).toString(16)
	    // }
	  }]);
	  return Schedule;
	}();

	exports.Schedule = Schedule;

}((this.BX.Up.Calendar = this.BX.Up.Calendar || {}),BX));
