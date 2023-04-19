import {Type} from 'main.core';

export class Schedule
{
	constructor(options = {})
	{
		this.idTeam = options.idTeam;
		this.rootNodeId = options.rootNodeId;
		this.rootNode = document.getElementById(this.rootNodeId);
		// console.log(this.rootNodeId);
		this.renderCalendar();
	}

	getEventsList(idTeam)
	{
		return new Promise((resolve, reject) => {
			BX.ajax.runAction(
					'up:calendar.calendar.getEventsList',
					{data: {
							idTeam: Number(idTeam),
						},
					})
				.then((response) => {
					const eventsList = response.data.events;
					const regularEventsList = response.data.regularEvents;

					resolve(eventsList, regularEventsList);
				})
				.catch((error) => {
					reject(error);
				})
			;
		});
	}

	renderCalendar()
	{
		let divCalendar = this.rootNode;
		console.log(divCalendar);
	}
	setName(name)
	{
		if (Type.isString(name))
		{
			this.name = name;
		}
	}

	getName()
	{
		return this.name;
	}
}