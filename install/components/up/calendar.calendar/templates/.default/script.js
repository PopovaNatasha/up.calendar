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
}

document.addEventListener('DOMContentLoaded', () => {
	const prevBtn = document.getElementById("prevBtn");
	const nextBtn = document.getElementById("nextBtn");
	const todayBtn = document.getElementById("todayBtn");

	prevBtn.addEventListener("click", e => {
		CalendarEventsList.calendar.prev();
	});

	nextBtn.addEventListener("click", e => {
		CalendarEventsList.calendar.next();
	});

	todayBtn.addEventListener("click", e => {
		CalendarEventsList.calendar.today();
	});
});