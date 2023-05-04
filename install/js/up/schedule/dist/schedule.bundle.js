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
          var changedEvents = this.changedEvents;
          eventsList.forEach(function (event) {
            var changedEventsById = changedEvents.filter(function (element) {
              return element['ID_EVENT'] === event['ID'];
            });
            var repeatUntil = event['DATE_END'] ? moment(event['DATE_END']).format('YYYY-MM-DD') : '2023-12-31';
            var dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
            var dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
            var dayStep = Number(event['DAY_STEP']);
            var dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
            var _loop = function _loop() {
              var regularEvent = event;
              regularEvent['START'] = dayTimeStart;
              regularEvent['END'] = dayTimeEnd;
              if (changedEventsById.length > 0) {
                changedEventsById.forEach(function (changedEvent) {
                  var dayStartChanged = moment(changedEvent['DATE_TIME_FROM']).format('YYYY-MM-DD');
                  if (moment(dayStartChanged).isSame(dayStart)) {
                    regularEvent = changedEvent;
                    regularEvent['START'] = moment(changedEvent['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
                    regularEvent['END'] = moment(changedEvent['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
                  }
                });
              }
              calendar.createEvents([{
                id: event['ID'],
                calendarId: regularEvent['ID_TEAM'],
                title: regularEvent['TITLE'],
                start: regularEvent['START'],
                end: regularEvent['END'],
                category: 'time',
                recurrenceRule: event['DAY_STEP']
              }]);
              dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
              dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
              dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
            };
            while (moment(dayTimeStart).isBefore(repeatUntil)) {
              _loop();
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
          var changedEvents = this.changedEvents;
          eventsList.forEach(function (event) {
            var changedEventsById = changedEvents.filter(function (element) {
              return element['ID_EVENT'] === event['ID'];
            });
            var repeatUntil = event['DATE_END'] ? moment(event['DATE_END']).format('YYYY-MM-DD') : '2023-12-31';
            var dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
            var dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
            var dayStep = Number(event['DAY_STEP']);
            var _loop2 = function _loop2() {
              var nowDay = moment().format('YYYY-MM-DD');
              var dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
              if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart)) {
                var regularEvent = event;
                regularEvent['START'] = dayTimeStart;
                regularEvent['END'] = dayTimeEnd;
                if (changedEventsById.length > 0) {
                  changedEventsById.forEach(function (changedEvent) {
                    var dayStartChanged = moment(changedEvent['DATE_TIME_FROM']).format('YYYY-MM-DD');
                    if (moment(dayStartChanged).isSame(dayStart)) {
                      regularEvent = changedEvent;
                      regularEvent['START'] = moment(changedEvent['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
                      regularEvent['END'] = moment(changedEvent['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
                    }
                  });
                }
                calendar.createEvents([{
                  id: regularEvent['ID'],
                  calendarId: regularEvent['ID_TEAM'],
                  title: regularEvent['TITLE'],
                  start: regularEvent['START'],
                  end: regularEvent['END'],
                  category: 'time',
                  recurrenceRule: event['DAY_STEP']
                }]);
              }
              dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
              dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
            };
            while (moment(dayTimeStart).isBefore(repeatUntil)) {
              _loop2();
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
            _this2.event = event;
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
            if (_this2.isUser) {
              calendarTeam = _this2.geCalendarById(event.calendarId);
              document.getElementById('popupDetailTeam').innerHTML = calendarTeam.name;
              document.getElementById('popupDetailDot').style.backgroundColor = calendarTeam.color;
              document.getElementById('popupTopLine').style.backgroundColor = calendarTeam.color;
            } else {
              document.getElementById('popupTopLine').style.backgroundColor = '#a1b56c';
            }
            popupForm.style.display = 'block';
            if (!_this2.isUser) {
              _this2.changeEventForm(_this2.event);
              _this2.setViewRule(event);
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
          var _this3 = this;
          var dayStep = document.getElementById('changeSelectCount').value;
          var selectRepeat = document.getElementById('changeSelectRepeat').value;
          if (selectRepeat === 'weekly') {
            dayStep = '7';
          }
          var dateFrom = document.getElementsByClassName('datetimepicker-dummy-input')[2].value;
          var dateTo = document.getElementsByClassName('datetimepicker-dummy-input')[3].value;
          return new Promise(function (resolve, reject) {
            BX.ajax.runAction('up:calendar.calendar.changeEvent', {
              data: {
                event: {
                  idEvent: document.getElementById('popupChangeEventId').value,
                  titleEvent: document.getElementById('popupChangeTitle').value,
                  dateFrom: dateFrom,
                  dateTo: dateTo,
                  dayStep: dayStep,
                  idTeam: _this3.idTeam,
                  isAll: document.getElementById('checkboxIsAll').checked
                }
              }
            }).then(function (response) {
              if (response.data) {
                _this3.calendar.clear();
                _this3.reload();
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
          var _this4 = this;
          var event = this.event;
          // let dayStep = document.getElementById('changeSelectCount').value;
          // let selectRepeat = document.getElementById('changeSelectRepeat').value;
          // if (selectRepeat === 'weekly') {
          // 	dayStep = '7';
          // }
          // let dateFrom = document.getElementsByClassName('datetimepicker-dummy-input')[2].value;
          // let dateTo = document.getElementsByClassName('datetimepicker-dummy-input')[3].value;

          return new Promise(function (resolve, reject) {
            BX.ajax.runAction('up:calendar.calendar.deleteEvent', {
              data: {
                event: {
                  idEvent: event.id,
                  dateFrom: event.start.toDate(),
                  dateTo: event.end.toDate(),
                  dayStep: event.recurrenceRule,
                  idTeam: _this4.idTeam,
                  isAll: document.getElementById('checkboxDeleteIsAll').checked
                }
              }
            }).then(function (response) {
              if (response.data) {
                _this4.calendar.clear();
                _this4.reload();
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
          // checkboxLabel = document.getElementById('checkboxIsAllLabel');
          checkboxChange = document.getElementById('checkboxIsAll');
          checkboxDelete = document.getElementById('checkboxDeleteIsAll');
          blockRepeat = document.getElementById('changeRepeat');
          checkboxChange.checked = false;
          checkboxDelete.checked = false;
          blockRepeat.style.display = 'none';
          if (event.recurrenceRule) {
            // checkboxLabel.style.display = 'block';
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
