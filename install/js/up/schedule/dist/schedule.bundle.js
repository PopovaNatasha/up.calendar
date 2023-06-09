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
        this.event = null;
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
            _this.changedEvents = eventsList['changedEvents'];
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
            useDetailPopup: true,
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
          var _this2 = this;
          var eventsList = this.singleEventsList;
          eventsList.forEach(function (event) {
            var dayTimeStart = _this2.formatToDateTime(event['DATE_TIME_FROM']);
            var dayTimeEnd = _this2.formatToDateTime(event['DATE_TIME_TO']);
            _this2.createEvent(event['ID'], event['ID_TEAM'], event['TITLE'], dayTimeStart, dayTimeEnd);
          });
        }
      }, {
        key: "formatToDateTime",
        value: function formatToDateTime(dateTime) {
          return moment(dateTime).format('YYYY-MM-DDTHH:mm:ss');
        }
      }, {
        key: "formatToDate",
        value: function formatToDate(dateTime) {
          return moment(dateTime).format('YYYY-MM-DD');
        }
      }, {
        key: "addRegularEvents",
        value: function addRegularEvents() {
          var _this3 = this;
          var eventsList = this.regularEventsList;
          var changedEvents = this.changedEvents;
          eventsList.forEach(function (event) {
            var changedEventsById = changedEvents.filter(function (element) {
              return element['ID_EVENT'] === event['ID'];
            });
            var repeatUntil = event['DATE_END'] ? _this3.formatToDate(event['DATE_END']) : '2023-12-31';
            var dayTimeStart = _this3.formatToDateTime(event['DATE_TIME_FROM']);
            var dayTimeEnd = _this3.formatToDateTime(event['DATE_TIME_TO']);
            var dayStep = Number(event['DAY_STEP']);
            var dayStart = _this3.formatToDate(dayTimeStart);
            while (moment(dayTimeStart).isBefore(repeatUntil)) {
              if (changedEventsById.length > 0) {
                changedEventsById.forEach(function (changedEvent) {
                  var dayStartChanged = _this3.formatToDate(changedEvent['DATE_TIME_FROM']);
                  if (moment(dayStartChanged).isSame(dayStart) && !changedEvent['DELETED']) {
                    var changedEventStart = _this3.formatToDateTime(changedEvent['DATE_TIME_FROM']);
                    var changedEventEnd = _this3.formatToDateTime(changedEvent['DATE_TIME_TO']);
                    _this3.createEvent(event['ID'], changedEvent['ID_TEAM'], changedEvent['TITLE'], changedEventStart, changedEventEnd, event['DAY_STEP']);
                  } else {
                    _this3.createEvent(event['ID'], event['ID_TEAM'], event['TITLE'], dayTimeStart, dayTimeEnd, event['DAY_STEP']);
                  }
                });
              } else {
                _this3.createEvent(event['ID'], event['ID_TEAM'], event['TITLE'], dayTimeStart, dayTimeEnd, event['DAY_STEP']);
              }
              dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
              dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
              dayStart = _this3.formatToDate(dayTimeStart);
            }
          });
        }
      }, {
        key: "createEvent",
        value: function createEvent(id, calendarId, title, start, end) {
          var recurrenceRule = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : '';
          var calendar = this.calendar;
          calendar.createEvents([{
            id: id,
            calendarId: calendarId,
            title: title,
            start: start,
            end: end,
            category: 'time',
            recurrenceRule: recurrenceRule
          }]);
        }
      }, {
        key: "addEventsForUser",
        value: function addEventsForUser() {
          var _this4 = this;
          var eventsList = this.singleEventsList;
          var storyEventList = this.userStoryEvents;
          console.log(storyEventList);
          eventsList.forEach(function (event) {
            var dayTimeStart = _this4.formatToDateTime(event['DATE_TIME_FROM']);
            var dayTimeEnd = _this4.formatToDateTime(event['DATE_TIME_TO']);
            var nowDay = _this4.formatToDate(moment());
            var dayStart = _this4.formatToDate(dayTimeStart);
            if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart)) {
              _this4.createEvent(event['ID'], event['ID_TEAM'], event['TITLE'], dayTimeStart, dayTimeEnd);
            }
          });
          storyEventList.forEach(function (event) {
            var dayTimeStart = _this4.formatToDateTime(event['DATE_TIME_FROM']);
            var dayTimeEnd = _this4.formatToDateTime(event['DATE_TIME_TO']);
            if (!event['DAY_STEP']) {
              _this4.createEvent(event['ID'], 'story', event['TITLE_EVENT'], dayTimeStart, dayTimeEnd);
            }
          });
        }
      }, {
        key: "addRegularEventsForUser",
        value: function addRegularEventsForUser() {
          var _this5 = this;
          var eventsList = this.regularEventsList;
          var storyEventList = this.userStoryEvents;
          var changedEvents = this.changedEvents;
          var nowDay = this.formatToDate(moment());
          eventsList.forEach(function (event) {
            var changedEventsById = changedEvents.filter(function (element) {
              return element['ID_EVENT'] === event['ID'];
            });
            var repeatUntil = event['DATE_END'] ? _this5.formatToDate(event['DATE_END']) : '2023-12-31';
            var dayTimeStart = _this5.formatToDateTime(event['DATE_TIME_FROM']);
            var dayTimeEnd = _this5.formatToDateTime(event['DATE_TIME_TO']);
            var dayStep = Number(event['DAY_STEP']);
            var _loop = function _loop() {
              var dayStart = _this5.formatToDate(dayTimeStart);
              if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart)) {
                var regularEvent = event;
                regularEvent['START'] = dayTimeStart;
                regularEvent['END'] = dayTimeEnd;
                if (changedEventsById.length > 0) {
                  changedEventsById.forEach(function (changedEvent) {
                    var dayStartChanged = _this5.formatToDate(changedEvent['DATE_TIME_FROM']);
                    if (moment(dayStartChanged).isSame(dayStart)) {
                      regularEvent = changedEvent;
                      regularEvent['START'] = _this5.formatToDateTime(changedEvent['DATE_TIME_FROM']);
                      regularEvent['END'] = _this5.formatToDateTime(changedEvent['DATE_TIME_TO']);
                    }
                  });
                }
                _this5.createEvent(regularEvent['ID'], regularEvent['ID_TEAM'], regularEvent['TITLE'], regularEvent['START'], regularEvent['END'], event['DAY_STEP']);
              }
              dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
              dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
            };
            while (moment(dayTimeStart).isBefore(repeatUntil)) {
              _loop();
            }
          });
          storyEventList.forEach(function (event) {
            if (event['DAY_STEP']) {
              var dayTimeStart = _this5.formatToDateTime(event['DATE_TIME_FROM']);
              var dayTimeEnd = _this5.formatToDateTime(event['DATE_TIME_TO']);
              var dayStart = _this5.formatToDate(dayTimeStart);
              var dayStep = Number(event['DAY_STEP']);
              while (moment(dayStart).isBefore(nowDay)) {
                _this5.createEvent(event['ID'], 'story', event['TITLE_EVENT'], dayTimeStart, dayTimeEnd, event['DAY_STEP']);
                dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                dayStart = _this5.formatToDate(dayTimeStart);
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
          var calendars = [{
            id: 'story',
            name: 'Прошедшие события',
            color: '#bbb',
            backgroundColor: '#bbb',
            borderColor: '#a1b56c',
            dragBackgroundColor: '#bbb'
          }];
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
          var _this6 = this;
          this.calendar.on('clickEvent', function (_ref) {
            var event = _ref.event;
            _this6.event = event;
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
            document.getElementById('popupDetailRecurrenceRule').innerHTML = event.recurrenceRule ? 'каждые ' + event.recurrenceRule + ' дней' : 'не повторяется';
            if (_this6.isUser) {
              calendarTeam = _this6.geCalendarById(event.calendarId);
              document.getElementById('popupDetailTeam').innerHTML = calendarTeam.name;
              document.getElementById('popupDetailDot').style.backgroundColor = calendarTeam.color;
              document.getElementById('popupTopLine').style.backgroundColor = calendarTeam.color;
            } else {
              document.getElementById('popupTopLine').style.backgroundColor = '#a1b56c';
            }
            popupForm.style.display = 'block';
            if (!_this6.isUser) {
              _this6.changeEventForm(_this6.event);
              _this6.setViewRule(event);
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
      }, {
        key: "changeEvent",
        value: function changeEvent() {
          var _this7 = this;
          var dayStep = document.getElementById('changeSelectCount').value;
          var selectRepeat = document.getElementById('changeSelectRepeat').value;
          if (selectRepeat === 'weekly') {
            dayStep = '7';
          }
          var dateFrom = document.getElementsByClassName('datetimepicker-dummy-input')[2].value;
          var dateTo = document.getElementsByClassName('datetimepicker-dummy-input')[3].value;
          console.log(this.event);
          var dateFromOrigin = moment(this.event.start.toDate()).format('DD.MM.YYYY HH:mm');
          return new Promise(function (resolve, reject) {
            BX.ajax.runAction('up:calendar.calendar.changeEvent', {
              data: {
                event: {
                  idEvent: document.getElementById('popupChangeEventId').value,
                  titleEvent: document.getElementById('popupChangeTitle').value,
                  dateFrom: dateFrom,
                  dateTo: dateTo,
                  dayStep: dayStep,
                  idTeam: _this7.idTeam,
                  isAll: document.getElementById('checkboxIsAll').checked,
                  dateFromOrigin: dateFromOrigin
                }
              }
            }).then(function (response) {
              if (response.data) {
                _this7.calendar.clear();
                _this7.reload();
              } else {
                alert('Не удалось изменить событие');
              }
            })["catch"](function (error) {
              reject(error);
            });
          });
        }
      }, {
        key: "deleteEvent",
        value: function deleteEvent() {
          var _this8 = this;
          var event = this.event;
          return new Promise(function (resolve, reject) {
            BX.ajax.runAction('up:calendar.calendar.deleteEvent', {
              data: {
                event: {
                  idEvent: event.id,
                  titleEvent: event.title,
                  dateFrom: moment(event.start.toDate()).format('DD.MM.YYYY HH:mm'),
                  dateTo: moment(event.end.toDate()).format('DD.MM.YYYY HH:mm'),
                  dayStep: event.recurrenceRule,
                  idTeam: _this8.idTeam,
                  isAll: document.getElementById('checkboxDeleteIsAll').checked
                }
              }
            }).then(function (response) {
              if (response.data) {
                _this8.calendar.clear();
                _this8.reload();
              } else {
                alert('Не удалось удалить событие');
              }
            })["catch"](function (error) {
              reject(error);
            });
          });
        }
      }, {
        key: "displayElementById",
        value: function displayElementById(idElement, display) {
          var element = document.getElementById(idElement);
          element.style.display = display;
        }
      }, {
        key: "setViewRule",
        value: function setViewRule(event) {
          var checkboxChange, checkboxDelete, blockRepeat;
          checkboxChange = document.getElementById('checkboxIsAll');
          checkboxDelete = document.getElementById('checkboxDeleteIsAll');
          blockRepeat = document.getElementById('changeRepeat');
          checkboxChange.checked = false;
          checkboxDelete.checked = false;
          blockRepeat.style.display = 'none';
          if (event.recurrenceRule) {
            this.displayElementById('checkboxIsAllLabel', 'block');
            this.displayElementById('checkboxDeleteIsAllLabel', 'block');
            checkboxChange.addEventListener('change', function (e) {
              if (e.target.checked) {
                blockRepeat.style.display = 'block';
              } else {
                blockRepeat.style.display = 'none';
              }
            });
            if (event.recurrenceRule !== '7') {
              document.getElementById('changeSelectRepeat').value = 'daily';
              document.getElementById('change-every').style.display = "block";
              document.getElementById('change-day-count').style.display = "block";
              document.getElementById('change-day').style.display = "block";
              document.getElementById('changeSelectCount').value = event.recurrenceRule;
            } else {
              document.getElementById('changeSelectRepeat').value = 'weekly';
              document.getElementById('change-every').style.display = "none";
              document.getElementById('change-day-count').style.display = "none";
              document.getElementById('change-day').style.display = "none";
              document.getElementById('changeSelectCount').value = '1';
            }
          } else {
            this.displayElementById('checkboxIsAllLabel', 'none');
            this.displayElementById('checkboxDeleteIsAllLabel', 'none');
            blockRepeat.style.display = 'none';
            document.getElementById('changeSelectCount').value = '';
          }
        }
      }]);
      return Schedule;
    }();

    exports.Schedule = Schedule;

}((this.BX.Up.Calendar = this.BX.Up.Calendar || {}),BX));
