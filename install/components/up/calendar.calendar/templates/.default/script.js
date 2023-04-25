function changeView(evt, tabName)
{
	console.log(tabName);
	var i, tablinks;

	tablinks = document.getElementsByClassName("tab");
	for (i = 0; i < 3; i++) {
		tablinks[i].className = tablinks[i].className.replace(" is-active", "");
	}
	CalendarEventsList.calendar.changeView(tabName);
	evt.currentTarget.className += " is-active";
	setRenderRangeText();
}

function setRenderRangeText() {
	var renderRange = document.getElementById('renderRange');
	let calendar = CalendarEventsList.calendar;
	moment.locale('ru');
	console.log(renderRange);
	var options = calendar.getOptions();
	var viewName = calendar.getViewName();
	var html = [];
	if (viewName === 'day') {
		html.push(moment(calendar.getDate().getTime()).format('DD MMMM YYYY'));
	} else if (viewName === 'month' &&
		(!options.month.visibleWeeksCount || options.month.visibleWeeksCount > 4)) {
		html.push(moment(calendar.getDate().getTime()).format('MMMM YYYY'));
	} else {
		html.push(moment(calendar.getDateRangeStart().getTime()).format('DD MMM YYYY'));
		html.push(' - ');
		html.push(moment(calendar.getDateRangeEnd().getTime()).format('DD MMM YYYY'));
	}
	renderRange.innerHTML = html.join('');
}

document.addEventListener('DOMContentLoaded', () => {
	const prevBtn = document.getElementById("prevBtn");
	const nextBtn = document.getElementById("nextBtn");
	const todayBtn = document.getElementById("todayBtn");
	setRenderRangeText();

	prevBtn.addEventListener("click", e => {
		CalendarEventsList.calendar.prev();
		setRenderRangeText();
	});

	nextBtn.addEventListener("click", e => {
		CalendarEventsList.calendar.next();
		setRenderRangeText();
	});

	todayBtn.addEventListener("click", e => {
		CalendarEventsList.calendar.today();
		setRenderRangeText();
	});
});