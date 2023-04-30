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
	    this.teams = options.teams;
	    this.isUser = options.isUser;
	    this.singleEventsList = [];
	    this.regularEventsList = [];
	    this.userStoryEvents = [];
	    if (this.isUser) {
	      this.setCheckboxBackgroundColor();
	    }
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
	          _this.setVisibleCalendar();
	          _this.addEventsForUser();
	          _this.addRegularEventsForUser();
	        } else {
	          _this.addEvents();
	          _this.addRegularEvents();
	        }
	        _this.AddOpenEventDetailPopup();
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
	        isReadOnly: true,
	        // showSlidebar: true,
	        // showMenu: true,
	        // useFormPopup: true,
	        useDetailPopup: true,
	        // useCreationPopup: false,
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
	          eventView: ['time']
	        },
	        month: {
	          dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
	          startDayOfWeek: 1,
	          narrowWeekend: false
	        },
	        calendars: this.getCalendarsList()
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
	            category: 'time',
	            recurrenceRule: 'каждые ' + event['DAY_STEP'] + ' дней'
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
	        if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart)) {
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
	          if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart)) {
	            calendar.createEvents([{
	              id: event['ID'],
	              calendarId: event['ID_TEAM'],
	              title: event['TITLE'],
	              start: dayTimeStart,
	              end: dayTimeEnd,
	              category: 'time',
	              recurrenceRule: 'каждые ' + event['DAY_STEP'] + ' дней'
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
	    key: "setVisibleCalendar",
	    value: function setVisibleCalendar() {
	      var idCalendars = this.idTeam;
	      var sidebar = document.querySelector('.sidebar');
	      var calendar = this.calendar;
	      sidebar.addEventListener('click', function (e) {
	        if ('value' in e.target) {
	          if (idCalendars.indexOf(e.target.value) > -1) {
	            idCalendars.splice(idCalendars.indexOf(e.target.value), 1);
	            calendar.setCalendarVisibility(e.target.value, false);
	          } else {
	            idCalendars.push(e.target.value);
	            calendar.setCalendarVisibility(e.target.value, true);
	          }
	        }
	      });
	    }
	  }, {
	    key: "setCheckboxBackgroundColor",
	    value: function setCheckboxBackgroundColor() {
	      var teams = this.teams;
	      teams.forEach(function (team) {
	        var color = team['COLOR'] ? team['COLOR'] : '#a1b56c';
	        var id = team['ID_TEAM'];
	        var checkbox = document.getElementById('chbox-' + id);
	        checkbox.style.setProperty('background-color', checkbox.checked ? color : '#fff');
	        checkbox.addEventListener('click', function () {
	          checkbox.style.setProperty('background-color', checkbox.checked ? color : '#fff');
	        });
	      });
	    }
	  }, {
	    key: "getCalendarsList",
	    value: function getCalendarsList() {
	      var teams = this.teams;
	      var calendars = [];
	      if (this.isUser) {
	        teams.forEach(function (team) {
	          var color = team['COLOR'];
	          calendars.push({
	            id: team['ID_TEAM'],
	            name: team['TITLE'],
	            color: color ? color : '#a1b56c',
	            backgroundColor: color ? color : '#a1b56c',
	            borderColor: color ? color : '#a1b56c',
	            dragBackgroundColor: color ? color : '#a1b56c'
	          });
	        });
	      }
	      calendars.push({
	        id: 'story',
	        name: 'Прошедшие события',
	        color: '#bbb',
	        backgroundColor: '#bbb',
	        borderColor: '#a1b56c',
	        dragBackgroundColor: '#bbb'
	      });
	      return calendars;
	    }
	  }, {
	    key: "geCalendarById",
	    value: function geCalendarById(calendarId) {
	      var teamCalendar;
	      var calendars = this.getCalendarsList();
	      calendars.forEach(function (calendar) {
	        if (calendar['id'] === calendarId) {
	          teamCalendar = calendar;
	        }
	      });
	      return teamCalendar;
	    }
	  }, {
	    key: "AddOpenEventDetailPopup",
	    value: function AddOpenEventDetailPopup() {
	      var _this2 = this;
	      this.calendar.on('clickEvent', function (_ref) {
	        var event = _ref.event;
	        var popupForm, eventElem, coordinates;
	        popupForm = document.getElementById('event-detail-popup');
	        eventElem = window.event.srcElement;
	        coordinates = eventElem.getBoundingClientRect();
	        popupForm.style.left = coordinates.right + 'px';
	        popupForm.style.top = coordinates.top - 85 + 'px';
	        var start, end, calendarTeam;
	        document.getElementById('popupDetailTitle').innerHTML = event.title;
	        start = moment(event.start.toDate()).format('DD.MM.YY HH:mm');
	        end = moment(event.end.toDate()).format('HH:mm');
	        document.getElementById('popupDetailDate').innerHTML = start + ' - ' + end;
	        document.getElementById('popupDetailRecurrenceRule').innerHTML = event.recurrenceRule ? event.recurrenceRule : 'не повторяется';
	        if (_this2.isUser) {
	          calendarTeam = _this2.geCalendarById(event.calendarId);
	          document.getElementById('popupDetailTeam').innerHTML = calendarTeam.name;
	          document.getElementById('popupDetailDot').style.backgroundColor = calendarTeam.color;
	          document.getElementById('popupTopLine').style.backgroundColor = calendarTeam.color;
	        } else {
	          document.getElementById('popupTopLine').style.backgroundColor = '#a1b56c';
	        }
	        popupForm.style.display = 'block';
	        _this2.eventForClosePopup();
	        _this2.changeEventForm(event);
	      });
	    }
	  }, {
	    key: "eventForClosePopup",
	    value: function eventForClosePopup() {
	      var popupForm = document.getElementById('event-detail-popup');
	      document.addEventListener('mousedown', function (e) {
	        var withinBoundaries = e.composedPath().includes(popupForm);
	        if (!withinBoundaries) {
	          popupForm.style.display = 'none'; // скрываем элемент т к клик был за его пределами
	        }
	      });
	    }
	  }, {
	    key: "changeEventForm",
	    value: function changeEventForm(event) {
	      document.getElementById('popupChangeEventId').value = event.id;
	      document.getElementById('popupChangeTitle').value = event.title;
	      EventDatePickers[1].clear();
	      EventDatePickers[1].value(event.start.toDate());
	      var endDate = document.getElementsByClassName('datetimepicker-dummy-input')[3];
	      endDate.value = moment(event.end.toDate()).format('DD.MM.YYYY HH:mm');
	    }
	  }]);
	  return Schedule;
	}();

	exports.Schedule = Schedule;

}((this.BX.Up.Calendar = this.BX.Up.Calendar || {}),BX));
