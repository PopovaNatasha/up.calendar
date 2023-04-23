import {Type} from 'main.core';

export class Schedule
{
	constructor(options = {})
	{
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

	reload()
	{
		this.loadEventsList(this.idTeam)
			.then(eventsList => {
				this.singleEventsList = eventsList['singleEvents'];
				this.regularEventsList = eventsList['regularEvents'];
				this.userStoryEvents = eventsList['userStoryEvents'];

				if (this.isUser)
				{
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
					{data: {
							idTeam: idTeam,
						},
					})
				.then((response) => {
					const eventsList = response.data.events;
					console.log(eventsList);

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
				hourStart: 6,
				hourEnd: 23,
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
				}
			]);
		});
		console.log(eventsList);
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
						calendarId: 'team',
						title: event['TITLE'],
						start: dayTimeStart,
						end: dayTimeEnd,
						category: 'time',
					}
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
			if (moment(nowDay).isBefore(dayStart))
			{
				calendar.createEvents([
					{
						id: event['ID'],
						calendarId: event['ID_TEAM'],
						title: event['TITLE'],
						start: dayTimeStart,
						end: dayTimeEnd,
						category: 'time',
					}
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
					}
				]);
			}
		})
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
				if (moment(nowDay).isBefore(dayStart))
				{
					calendar.createEvents([
						{
							id: event['ID'],
							calendarId: 'team',
							title: event['TITLE'],
							start: dayTimeStart,
							end: dayTimeEnd,
							category: 'time',
						}
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
							calendarId: 'team',
							title: event['TITLE_EVENT'],
							start: dayTimeStart,
							end: dayTimeEnd,
							category: 'time',
						}
					]);
					dayTimeStart = moment(dayTimeStart).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
					dayTimeEnd = moment(dayTimeEnd).add(dayStep, 'days').format('YYYY-MM-DDTHH:mm:ss');
				}
			}
		});
	}

	getIdCalendars()
	{
		let idTeams = this.idTeam;
		let calendarId = [];
		idTeams.forEach(idTeam => {
			calendarId.push({id: idTeam});
		});
		return calendarId;
	}
}