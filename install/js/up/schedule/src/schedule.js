import {Type} from 'main.core';

export class Schedule {
    constructor(options = {}) {
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
            useDetailPopup: true,
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
        eventsList.forEach(event => {
			let dayTimeStart = this.formatToDateTime(event['DATE_TIME_FROM']);
			let dayTimeEnd = this.formatToDateTime(event['DATE_TIME_TO']);
			this.createEvent(event['ID'], event['ID_TEAM'], event['TITLE'], dayTimeStart, dayTimeEnd);
        });
    }

	formatToDateTime(dateTime)
	{
		return moment(dateTime).format('YYYY-MM-DDTHH:mm:ss');
	}

	formatToDate(dateTime)
	{
		return moment(dateTime).format('YYYY-MM-DD');
	}

    addRegularEvents() {
        let eventsList = this.regularEventsList;
		let changedEvents = this.changedEvents;
        eventsList.forEach(event => {
			let changedEventsById = changedEvents.filter(element => element['ID_EVENT'] === event['ID']);
			let repeatUntil = event['DATE_END'] ? this.formatToDate(event['DATE_END']) : '2023-12-31';
            let dayTimeStart = this.formatToDateTime(event['DATE_TIME_FROM']);
            let dayTimeEnd = this.formatToDateTime(event['DATE_TIME_TO']);
            let dayStep = Number(event['DAY_STEP']);
			let dayStart = this.formatToDate(dayTimeStart);
            while (moment(dayTimeStart).isBefore(repeatUntil))
			{
				if (changedEventsById.length > 0)
				{
					changedEventsById.forEach(changedEvent => {
						let dayStartChanged = this.formatToDate(changedEvent['DATE_TIME_FROM']);

						if (moment(dayStartChanged).isSame(dayStart) && !changedEvent['DELETED'])
						{
							let changedEventStart = this.formatToDateTime(changedEvent['DATE_TIME_FROM']);
							let changedEventEnd = this.formatToDateTime(changedEvent['DATE_TIME_TO']);
							this.createEvent(event['ID'], changedEvent['ID_TEAM'], changedEvent['TITLE'], changedEventStart, changedEventEnd, event['DAY_STEP']);
						}
						else
						{
							this.createEvent(event['ID'], event['ID_TEAM'], event['TITLE'], dayTimeStart, dayTimeEnd, event['DAY_STEP']);
						}
					});
				}
				else
				{
					this.createEvent(event['ID'], event['ID_TEAM'], event['TITLE'], dayTimeStart, dayTimeEnd, event['DAY_STEP']);
				}
                dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
				dayStart = this.formatToDate(dayTimeStart);
            }
        });
    }

	createEvent(id, calendarId, title, start, end, recurrenceRule = '')
	{
		let calendar = this.calendar;

		calendar.createEvents([
			{
				id: id,
				calendarId: calendarId,
				title: title,
				start: start,
				end: end,
				category: 'time',
				recurrenceRule: recurrenceRule,
			},
		]);
	}

    addEventsForUser() {
        let eventsList = this.singleEventsList;
        let storyEventList = this.userStoryEvents;
        eventsList.forEach(event => {
            let dayTimeStart = this.formatToDateTime(event['DATE_TIME_FROM']);
            let dayTimeEnd = this.formatToDateTime(event['DATE_TIME_TO']);
            let nowDay = this.formatToDate(moment());
            let dayStart = this.formatToDate(dayTimeStart);
            if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart))
			{
				this.createEvent(event['ID'], event['ID_TEAM'], event['TITLE'], dayTimeStart, dayTimeEnd);
            }
        });
        storyEventList.forEach(event => {
            let dayTimeStart = this.formatToDateTime(event['DATE_TIME_FROM']);
            let dayTimeEnd = this.formatToDateTime(event['DATE_TIME_TO']);
            if (!event['DAY_STEP'])
			{
				this.createEvent(event['ID'], 'story', event['TITLE_EVENT'], dayTimeStart, dayTimeEnd);
            }
        });
    }

    addRegularEventsForUser() {
        let eventsList = this.regularEventsList;
        let storyEventList = this.userStoryEvents;
		let changedEvents = this.changedEvents;
		let nowDay = this.formatToDate(moment());
        eventsList.forEach(event => {
			let changedEventsById = changedEvents.filter(element => element['ID_EVENT'] === event['ID']);
			let repeatUntil = event['DATE_END'] ? this.formatToDate(event['DATE_END']) : '2023-12-31';
            let dayTimeStart = this.formatToDateTime(event['DATE_TIME_FROM']);
            let dayTimeEnd = this.formatToDateTime(event['DATE_TIME_TO']);
            let dayStep = Number(event['DAY_STEP']);
            while (moment(dayTimeStart).isBefore(repeatUntil)) {
                let dayStart = this.formatToDate(dayTimeStart);
                if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart))
				{
					let regularEvent = event;
					regularEvent['START'] = dayTimeStart;
					regularEvent['END'] = dayTimeEnd;

					if (changedEventsById.length > 0)
					{
						changedEventsById.forEach(changedEvent => {
							let dayStartChanged = this.formatToDate(changedEvent['DATE_TIME_FROM']);
							if (moment(dayStartChanged).isSame(dayStart))
							{
								regularEvent = changedEvent;
								regularEvent['START'] = this.formatToDateTime(changedEvent['DATE_TIME_FROM']);
								regularEvent['END'] = this.formatToDateTime(changedEvent['DATE_TIME_TO']);
							}
						});
					}
					this.createEvent(regularEvent['ID'], regularEvent['ID_TEAM'], regularEvent['TITLE'], regularEvent['START'], regularEvent['END'], event['DAY_STEP']);
                }
                dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
            }
        });
        storyEventList.forEach(event => {
            if (event['DAY_STEP']) {
                let dayTimeStart = this.formatToDateTime(event['DATE_TIME_FROM']);
                let dayTimeEnd = this.formatToDateTime(event['DATE_TIME_TO']);
				let dayStart = this.formatToDate(dayTimeStart);
                let dayStep = Number(event['DAY_STEP']);

                while (moment(dayStart).isBefore(nowDay)) {
					this.createEvent(event['ID'], 'story', event['TITLE_EVENT'], dayTimeStart, dayTimeEnd, event['DAY_STEP']);
                    dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
                    dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
					dayStart = this.formatToDate(dayTimeStart);
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
        let calendars = [
			{
				id: 'story',
				name: 'Прошедшие события',
				color: '#bbb',
				backgroundColor: '#bbb',
				borderColor: '#a1b56c',
				dragBackgroundColor: '#bbb',
			}
		];
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
			this.event = event;
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
				this.changeEventForm(this.event);
				this.setViewRule(event);
			}
        });
    }

    changeEventForm(event) {
        document.getElementById('popupChangeEventId').value = event.id;
        document.getElementById('popupChangeTitle').value = event.title;

        EventDatePickers[1].clear();
        EventDatePickers[1].value(event.start.toDate());
        let endDate = document.getElementsByClassName('datetimepicker-dummy-input')[3];
        endDate.value = moment(event.end.toDate()).format('DD.MM.YYYY HH:mm');
    }

    changeEvent() {
        let dayStep = document.getElementById('changeSelectCount').value;
        let selectRepeat = document.getElementById('changeSelectRepeat').value;
        if (selectRepeat === 'weekly') {
            dayStep = '7';
        }
        let dateFrom = document.getElementsByClassName('datetimepicker-dummy-input')[2].value;
        let dateTo = document.getElementsByClassName('datetimepicker-dummy-input')[3].value;
		let dateFromOrigin = moment(this.event.start.toDate()).format('DD.MM.YYYY HH:mm');

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
                            isAll: document.getElementById('checkboxIsAll').checked,
							dateFromOrigin: dateFromOrigin
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
						alert('Не удалось изменить событие');
					}
                })
                .catch((error) => {
                    reject(error);
                })
            ;
        });
    }

	deleteEvent()
	{
		let event = this.event;

		return new Promise((resolve, reject) => {
			BX.ajax.runAction(
					'up:calendar.calendar.deleteEvent',
					{
						data: {
							event: {
								idEvent: event.id,
								titleEvent: event.title,
								dateFrom: moment(event.start.toDate()).format('DD.MM.YYYY HH:mm'),
								dateTo: moment(event.end.toDate()).format('DD.MM.YYYY HH:mm'),
								dayStep: event.recurrenceRule,
								idTeam: this.idTeam,
								isAll: document.getElementById('checkboxDeleteIsAll').checked
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
						alert('Не удалось удалить событие');
					}
				})
				.catch((error) => {
					reject(error);
				})
			;
		});
	}

	displayElementById(idElement, display)
	{
		let element = document.getElementById(idElement);
		element.style.display = display;
	}

    setViewRule(event) {
        let checkboxChange, checkboxDelete, blockRepeat;
        checkboxChange = document.getElementById('checkboxIsAll');
		checkboxDelete = document.getElementById('checkboxDeleteIsAll');
        blockRepeat = document.getElementById('changeRepeat');
		checkboxChange.checked = false;
		checkboxDelete.checked = false;
        blockRepeat.style.display = 'none';

        if (event.recurrenceRule) {
			this.displayElementById('checkboxIsAllLabel', 'block');
			this.displayElementById('checkboxDeleteIsAllLabel', 'block');
			checkboxChange.addEventListener('change', e => {
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
			this.displayElementById('checkboxIsAllLabel', 'none');
			this.displayElementById('checkboxDeleteIsAllLabel', 'none');
            blockRepeat.style.display = 'none';
			document.getElementById('changeSelectCount').value = '';
        }
    }
}