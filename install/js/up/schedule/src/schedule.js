import {Type} from 'main.core';

export class Schedule
{
	constructor(options = {})
	{
		this.idTeam = options.idTeam;
		this.rootNodeId = options.rootNodeId;
		this.rootNode = document.getElementById(this.rootNodeId);

		this.singleEventsList = [];
		this.regularEventsList = [];
		this.calendar = this.createCalendar();
		this.reload();
	}

	reload()
	{
		this.loadEventsList(this.idTeam)
			.then(eventsList => {
				this.singleEventsList = eventsList['singleEvents'];
				this.regularEventsList = eventsList['regularEvents']
				this.addEvents();
				this.addRegularEvents();
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

	loadRegularEventsList(idTeam)
	{
		return new Promise((resolve, reject) => {
			BX.ajax.runAction(
					'up:calendar.calendar.getRegularEventsList',
					{data: {
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
			useFormPopup: false,
			useDetailPopup: false,
			useCreationPopup: false,
			defaultView: 'month',
			taskView: true,
			scheduleView: false,
			template: {
				milestone: function(schedule) {
					return '<span style="color:red;"><i class="fa fa-flag"></i> ' + schedule.title + '</span>';
				},
				milestoneTitle: function() {
					return 'Milestone';
				},
				task: function(schedule) {
					return '&nbsp;&nbsp;#' + schedule.title;
				},
				taskTitle: function() {
					return '<label><input type="checkbox" />Task</label>';
				},
				allday: function(schedule) {
					return schedule.title + ' <i class="fa fa-refresh"></i>';
				},
				alldayTitle: function() {
					return 'All Day';
				},
				time: function(schedule) {
					return '<strong>' + moment(schedule.start.getTime()).format('HH:mm') + '</strong> ' + schedule.title;
				}
			},
			week: {
				dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
				startDayOfWeek: 1,
				narrowWeekend: false,
				// taskView: false,
				eventView: ['time']
			},
			month: {
				dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
				startDayOfWeek: 1,
				narrowWeekend: false
			},
			// list of Calendars that can be used to add new schedule
			calendars: [
				{
					id: 'team',
					name: 'Personal',
					backgroundColor:  'red',
				}
			],
		});
	}

	addEvents()
	{
		let eventsList = this.singleEventsList;
		let calendar = this.calendar;
		eventsList.forEach(event => {
			let dayTimeStart = (event['DATE_TIME_FROM']).split('+');
			let dayTimeEnd = (event['DATE_TIME_TO']).split('+');
			calendar.createEvents([
				{
					id: event['ID'],
					calendarId: 'ream',
					title: event['TITLE'],
					start: dayTimeStart[0],
					end: dayTimeEnd[0],
					category: 'time',
				}
			]);
		});
		console.log(eventsList);
	}

	// addRegularEvents()
	// {
	// 	let eventsList = this.regularEventsList;
	// 	let calendar = this.calendar;
	// 	eventsList.forEach(event => {
	// 		let dayTimeStart = (event['DATE_TIME_FROM']).split('+');
	// 		let dayTimeEnd = (event['DATE_TIME_TO']).split('+');
	// 		calendar.createEvents([
	// 			{
	// 				id: event['ID'],
	// 				calendarId: 'ream',
	// 				title: event['TITLE'],
	// 				start: dayTimeStart[0],
	// 				end: dayTimeEnd[0],
	// 				category: 'time',
	// 			}
	// 		]);
	// 	});
	// 	console.log(eventsList);
	// }
}