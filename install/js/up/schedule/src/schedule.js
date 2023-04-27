import { Type } from 'main.core';

export class Schedule
{
	constructor(options = {})
	{
		this.idTeam = options.idTeam;
		this.rootNodeId = options.rootNodeId;
		this.rootNode = document.getElementById(this.rootNodeId);
		this.teams = options.teams;
		this.isUser = options.isUser;

		this.singleEventsList = [];
		this.regularEventsList = [];
		this.userStoryEvents = [];
		if (this.isUser)
		{
			this.setCheckboxBackgroundColor();
		}
		this.calendar = this.createCalendar();

		this.calendar.on('clickEvent', ({ event }) => {
			console.log(event); // EventObject
			let popupForm = document.getElementById('event-detail-popup');
			// let coordinates = event.getBoundingClientRect();
			// popupForm.style.left = coordinates.left + 'px';
			// popupForm.style.top = coordinates.bottom + 'px';
			let eventId, eventTitle, start, end, recurrenceRule, calendarId, calendarTitle;
			popupForm.style.display = 'block';
		});

		this.reload();
	}

	reload()
	{
		this.loadEventsList(this.idTeam)
			.then(eventsList => {
				this.singleEventsList = eventsList['singleEvents'];
				this.regularEventsList = eventsList['regularEvents'];
				this.userStoryEvents = eventsList['userStoryEvents'];

				if (this.isUser)
				{
					this.setVisibleCalendar();
					this.addEventsForUser();
					this.addRegularEventsForUser();
				}
				else
				{
					this.addEvents();
					this.addRegularEvents();
				}
			});
	}

	loadEventsList(idTeam)
	{
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

	createCalendar()
	{
		return new tui.Calendar(`#${this.rootNodeId}`, {
			isReadOnly: true,
			// showSlidebar: true,
			// showMenu: true,
			useFormPopup: false,
			useDetailPopup: true,
			useCreationPopup: false,
			defaultView: 'month',
			taskView: true,
			scheduleView: false,
			template: {
				task: function(schedule) {
					return '&nbsp;&nbsp;#' + schedule.title;
				},
				taskTitle: function() {
					return '<label><input type="checkbox" />Task</label>';
				},
				time: function(schedule) {
					return '<strong>' + moment(schedule.start.getTime()).format('HH:mm') + '</strong> ' + schedule.title;
				},
				timegridDisplayPrimaryTime: function(time) {
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

	addEvents()
	{
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

	addRegularEvents()
	{
		let eventsList = this.regularEventsList;
		let calendar = this.calendar;
		let repeatUntil = '2023-12-31';
		eventsList.forEach(event => {
			let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
			let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
			let dayStep = Number(event['DAY_STEP']);
			while (moment(dayTimeStart).isBefore(repeatUntil))
			{
				calendar.createEvents([
					{
						id: event['ID'],
						calendarId: event['ID_TEAM'],
						title: event['TITLE'],
						start: dayTimeStart,
						end: dayTimeEnd,
						category: 'time',
						recurrenceRule: 'каждые' + event['DAY_STEP'] + 'дней',
					},
				]);
				dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
				dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
			}
		});
	}

	addEventsForUser()
	{
		let eventsList = this.singleEventsList;
		let storyEventList = this.userStoryEvents;
		let calendar = this.calendar;
		eventsList.forEach(event => {
			let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
			let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
			let nowDay = moment().format('YYYY-MM-DD');
			let dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
			if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart))
			{
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
			if (!event['DAY_STEP'])
			{
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

	addRegularEventsForUser()
	{
		let eventsList = this.regularEventsList;
		let storyEventList = this.userStoryEvents;
		let calendar = this.calendar;
		let repeatUntil = '2023-12-31';
		eventsList.forEach(event => {
			let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
			let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
			let dayStep = Number(event['DAY_STEP']);
			while (moment(dayTimeStart).isBefore(repeatUntil))
			{
				let nowDay = moment().format('YYYY-MM-DD');
				let dayStart = moment(dayTimeStart).format('YYYY-MM-DD');
				if (moment(nowDay).isBefore(dayStart) || moment(nowDay).isSame(dayStart))
				{
					calendar.createEvents([
						{
							id: event['ID'],
							calendarId: event['ID_TEAM'],
							title: event['TITLE'],
							start: dayTimeStart,
							end: dayTimeEnd,
							category: 'time',
							recurrenceRule: 'каждые ' + event['DAY_STEP'] + ' дней',
						},
					]);
				}
				dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
				dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
			}
		});
		storyEventList.forEach(event => {
			if (event['DAY_STEP'])
			{
				let dayTimeStart = moment(event['DATE_TIME_FROM']).format('YYYY-MM-DDTHH:mm:ss');
				let dayTimeEnd = moment(event['DATE_TIME_TO']).format('YYYY-MM-DDTHH:mm:ss');
				let dayStep = Number(event['DAY_STEP']);

				let nowDay = moment().format('YYYY-MM-DD');
				while (moment(dayTimeStart).isBefore(nowDay))
				{
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

	setVisibleCalendar()
	{
		let idCalendars = this.idTeam;
		let sidebar = document.querySelector('.sidebar');
		let calendar = this.calendar;
		sidebar.addEventListener('click', function(e) {
			if ('value' in e.target)
			{
				if (idCalendars.indexOf(e.target.value) > -1)
				{
					idCalendars.splice(idCalendars.indexOf(e.target.value), 1);
					calendar.setCalendarVisibility(e.target.value, false);
				}
				else
				{
					idCalendars.push(e.target.value);
					calendar.setCalendarVisibility(e.target.value, true);
				}
			}
		});
	}

	setCheckboxBackgroundColor()
	{
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

	getCalendarsList()
	{
		let teams = this.teams;
		let calendars = [];
		if (this.isUser)
		{
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
}