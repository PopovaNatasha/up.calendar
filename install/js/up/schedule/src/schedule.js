import {Type} from 'main.core';

export class Schedule {
    constructor(options = {}) {
        this.idTeam = options.idTeam;
        console.log(this.idTeam);
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

    reload() {
        this.loadEventsList(this.idTeam)
            .then(eventsList => {
                this.singleEventsList = eventsList['singleEvents'];
                this.regularEventsList = eventsList['regularEvents'];
                this.userStoryEvents = eventsList['userStoryEvents'];
				this.changedEvents = eventsList['changedEvents'];

                if (this.isUser) {
                    this.setVisibleCalendar();
                    this.addEventsForUser();
                    this.addRegularEventsForUser();
                } else {
                    this.addEvents();
                    this.addRegularEvents();
                }
                this.AddOpenEventDetailPopup();
            });
    }

    loadEventsList(idTeam) {
        return new Promise((resolve, reject) => {
            BX.ajax.runAction(
                'up:calendar.calendar.getEventsList',
                {
                    data: {
                        idTeam: idTeam,
                    },
                })
                .then((response) => {
                    const eventsList = response.data.events;
                    resolve(eventsList);
                })
                .catch((error) => {
                    reject(error);
                })
            ;
        });
    }

    createCalendar() {
        return new tui.Calendar(`#${this.rootNodeId}`, {
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
                task: function (schedule) {
                    return '&nbsp;&nbsp;#' + schedule.title;
                },
                taskTitle: function () {
                    return '<label><input type="checkbox" />Task</label>';
                },
                time: function (schedule) {
                    return '<strong>' + moment(schedule.start.getTime()).format('HH:mm') + '</strong> ' + schedule.title;
                },
                timegridDisplayPrimaryTime: function (time) {
                    return time.time.d.getHours() + ':00';
                },
            },
            week: {
                dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                startDayOfWeek: 1,
                narrowWeekend: false,
                taskView: false,
                eventView: ['time'],
            },
            month: {
                dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                startDayOfWeek: 1,
                narrowWeekend: false,
            },
            calendars: this.getCalendarsList(),
        });
    }

    addEvents() {
        let eventsList = this.singleEventsList;
        let calendar = this.calendar;
        eventsList.forEach(event => {
            let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
            let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
            calendar.createEvents([
                {
                    id: event['ID'],
                    calendarId: event['ID_TEAM'],
                    title: event['TITLE'],
                    start: dayTimeStart,
                    end: dayTimeEnd,
                    category: 'time',
                },
            ]);
        });
    }

    addRegularEvents() {
        let eventsList = this.regularEventsList;
        let calendar = this.calendar;
		let changedEvents = this.changedEvents;
        eventsList.forEach(event => {
			let changedEventsById = changedEvents.filter(element => element['ID_EVENT'] === event['ID']);
			let repeatUntil = event['DATE_END'] ? moment(event['DATE_END']).format('YYYY-MM-DD') : '2023-12-31';
            let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
            let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
            let dayStep = Number(event['DAY_STEP']);
			let dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
            while (moment(dayTimeStart).isBefore(repeatUntil)) {
				let regularEvent = event;
				regularEvent['START'] = dayTimeStart;
				regularEvent['END'] = dayTimeEnd;

				if (changedEventsById.length > 0)
				{
					changedEventsById.forEach(changedEvent => {
						let dayStartChanged = moment(changedEvent['DATE_TIME_FROM']).format('YYYY-MM-DD');
						if (moment(dayStartChanged).isSame(dayStart))
						{
							regularEvent = changedEvent;
							regularEvent['START'] = moment(changedEvent['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
							regularEvent['END'] = moment(changedEvent['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
						}
					});
				}
                calendar.createEvents([
                    {
                        id: regularEvent['ID'],
                        calendarId: regularEvent['ID_TEAM'],
                        title: regularEvent['TITLE'],
                        start: regularEvent['START'],
                        end: regularEvent['END'],
                        category: 'time',
                        recurrenceRule: regularEvent['DAY_STEP'],
                    },
                ]);
                dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
				dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
            }
        });
    }

    addEventsForUser() {
        let eventsList = this.singleEventsList;
        let storyEventList = this.userStoryEvents;
        let calendar = this.calendar;
        eventsList.forEach(event => {
            let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
            let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
            let nowDay = moment().format('YYYY-MM-DD');
            let dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
            if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart)) {
                calendar.createEvents([
                    {
                        id: event['ID'],
                        calendarId: event['ID_TEAM'],
                        title: event['TITLE'],
                        start: dayTimeStart,
                        end: dayTimeEnd,
                        category: 'time',
                    },
                ]);
            }
        });
        storyEventList.forEach(event => {
            let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
            let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
            if (!event['DAY_STEP']) {
                calendar.createEvents([
                    {
                        id: event['ID'],
                        calendarId: 'story',
                        title: event['TITLE_EVENT'],
                        start: dayTimeStart,
                        end: dayTimeEnd,
                        category: 'time',
                    },
                ]);
            }
        });
    }

    addRegularEventsForUser() {
        let eventsList = this.regularEventsList;
        let storyEventList = this.userStoryEvents;
        let calendar = this.calendar;
		let changedEvents = this.changedEvents;
        eventsList.forEach(event => {
			let changedEventsById = changedEvents.filter(element => element['ID_EVENT'] === event['ID']);
			let repeatUntil = event['DATE_END'] ? moment(event['DATE_END']).format('YYYY-MM-DD') : '2023-12-31';
            let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
            let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
            let dayStep = Number(event['DAY_STEP']);
            while (moment(dayTimeStart).isBefore(repeatUntil)) {
                let nowDay = moment().format('YYYY-MM-DD');
                let dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
                if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart)) {
					let regularEvent = event;
					regularEvent['START'] = dayTimeStart;
					regularEvent['END'] = dayTimeEnd;

					if (changedEventsById.length > 0)
					{
						changedEventsById.forEach(changedEvent => {
							let dayStartChanged = moment(changedEvent['DATE_TIME_FROM']).format('YYYY-MM-DD');
							if (moment(dayStartChanged).isSame(dayStart))
							{
								regularEvent = changedEvent;
								regularEvent['START'] = moment(changedEvent['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
								regularEvent['END'] = moment(changedEvent['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
							}
						});
					}
                    calendar.createEvents([
                        {
                            id: regularEvent['ID'],
                            calendarId: regularEvent['ID_TEAM'],
                            title: regularEvent['TITLE'],
                            start: regularEvent['START'],
                            end: regularEvent['END'],
                            category: 'time',
                            recurrenceRule: event['DAY_STEP'],
                        },
                    ]);
                }
                dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
            }
        });
        storyEventList.forEach(event => {
            if (event['DAY_STEP']) {
                let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
                let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
                let dayStep = Number(event['DAY_STEP']);

                let nowDay = moment().format('YYYY-MM-DD');
                while (moment(dayTimeStart).isBefore(nowDay)) {
                    calendar.createEvents([
                        {
                            id: event['ID'],
                            calendarId: 'story',
                            title: event['TITLE_EVENT'],
                            start: dayTimeStart,
                            end: dayTimeEnd,
                            category: 'time',
                        },
                    ]);
                    dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                    dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                }
            }
        });
    }

    setVisibleCalendar() {
        let idCalendars = this.idTeam;
        let sidebar = document.querySelector('.sidebar');
        let calendar = this.calendar;
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

    setCheckboxBackgroundColor() {
        let teams = this.teams;
        teams.forEach(team => {
            let color = team['COLOR'] ? team['COLOR'] : '#a1b56c';
            let id = team['ID_TEAM'];
            let checkbox = document.getElementById('chbox-' + id);
            checkbox.style.setProperty('background-color', checkbox.checked ? color : '#fff');
            checkbox.addEventListener('click', () => {
                checkbox.style.setProperty('background-color', checkbox.checked ? color : '#fff');
            });
        });
    }

    getCalendarsList() {
        let teams = this.teams;
        let calendars = [];
        if (this.isUser) {
            teams.forEach(team => {
                let color = team['COLOR'];
                calendars.push({
                    id: team['ID_TEAM'],
                    name: team['TITLE'],
                    color: color ? color : '#a1b56c',
                    backgroundColor: color ? color : '#a1b56c',
                    borderColor: color ? color : '#a1b56c',
                    dragBackgroundColor: color ? color : '#a1b56c',
                });
            });
        }
        calendars.push({
            id: 'story',
            name: 'Прошедшие события',
            color: '#bbb',
            backgroundColor: '#bbb',
            borderColor: '#a1b56c',
            dragBackgroundColor: '#bbb',
        });
        return calendars;
    }

    geCalendarById(calendarId) {
        let teamCalendar;
        let calendars = this.getCalendarsList();
        calendars.forEach(calendar => {
            if (calendar['id'] === calendarId) {
                teamCalendar = calendar;
            }
        });
        return teamCalendar;
    }

    AddOpenEventDetailPopup() {
        this.calendar.on('clickEvent', ({event}) => {
            let popupForm, eventElem, coordinates;
            popupForm = document.getElementById('event-detail-popup');
            eventElem = window.event.srcElement;
            coordinates = eventElem.getBoundingClientRect();
            popupForm.style.left = coordinates.right + 'px';
            popupForm.style.top = (coordinates.top - 85) + 'px';

            let start, end, calendarTeam;
            document.getElementById('popupDetailTitle').innerHTML = event.title;
            start = moment(event.start.toDate()).format('DD.MM.YY HH:mm');
            end = moment(event.end.toDate()).format('HH:mm')
            document.getElementById('popupDetailDate').innerHTML = start + ' - ' + end;
            document.getElementById('popupDetailRecurrenceRule').innerHTML = event.recurrenceRule ? 'каждые ' + event.recurrenceRule + ' дней' : 'не повторяется';
            if (this.isUser) {
                calendarTeam = this.geCalendarById(event.calendarId);
                document.getElementById('popupDetailTeam').innerHTML = calendarTeam.name;
                document.getElementById('popupDetailDot').style.backgroundColor = calendarTeam.color;
                document.getElementById('popupTopLine').style.backgroundColor = calendarTeam.color;
            } else {
                document.getElementById('popupTopLine').style.backgroundColor = '#a1b56c';
            }
            popupForm.style.display = 'block';
			if (!this.isUser)
			{
				this.changeEventForm(event);
			}
        });
    }

    changeEventForm(event) {
        document.getElementById('popupChangeEventId').value = event.id;
        document.getElementById('popupChangeTitle').value = event.title;

        EventDatePickers[1].clear();
        EventDatePickers[1].value(event.start.toDate());
        console.log(document.getElementById('date').value)
        let endDate = document.getElementsByClassName('datetimepicker-dummy-input')[3];
        endDate.value = moment(event.end.toDate()).format('DD.MM.YYYY HH:mm');
        this.setViewRule(event);
		console.log(event);
    }

    changeEvent() {
        let dayStep = document.getElementById('changeSelectCount').value;
        let selectRepeat = document.getElementById('changeSelectRepeat').value;
        if (selectRepeat === 'weekly') {
            dayStep = '7';
        }
        let dateFrom = document.getElementsByClassName('datetimepicker-dummy-input')[2].value;
        let dateTo = document.getElementsByClassName('datetimepicker-dummy-input')[3].value;

        return new Promise((resolve, reject) => {
            BX.ajax.runAction(
                'up:calendar.calendar.changeEvent',
                {
                    data: {
                        event: {
                            idEvent: document.getElementById('popupChangeEventId').value,
                            titleEvent: document.getElementById('popupChangeTitle').value,
                            dateFrom: dateFrom,
                            dateTo: dateTo,
                            dayStep: dayStep,
                            idTeam: this.idTeam,
                            isAll: document.getElementById('checkboxIsAll').checked
                        }
                    },
                })
                .then((response) => {
					if (response.data)
					{
						this.calendar.clear();
						this.reload();
					}
					else
					{
						alert('Не удалось изменить соыбытие');
					}
                })
                .catch((error) => {
                    reject(error);
                })
            ;
        });
    }

    setViewRule(event) {
        let checkbox, checkboxLabel, blockRepeat;
        checkboxLabel = document.getElementById('checkboxIsAllLabel');
        checkbox = document.getElementById('checkboxIsAll');
        blockRepeat = document.getElementById('changeRepeat');
        checkbox.checked = false;
        blockRepeat.style.display = 'none';

        if (event.recurrenceRule) {
            checkboxLabel.style.display = 'block';
            checkboxLabel.addEventListener('change', e => {
                if (e.target.checked) {
                    blockRepeat.style.display = 'block';
                } else {
                    blockRepeat.style.display = 'none';
                }
            })
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
            checkboxLabel.style.display = 'none';
            blockRepeat.style.display = 'none';
			document.getElementById('changeSelectCount').value = '';
        }
    }
}