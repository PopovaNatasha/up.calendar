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
	let renderRange = document.getElementById('renderRange');
	let calendar = CalendarEventsList.calendar;
	moment.locale('ru');
	console.log(renderRange);
	let options = calendar.getOptions();
	let viewName = calendar.getViewName();
	let html = [];
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

function displayColorTeam() {
	let blocks = document.querySelectorAll('.control.team');
	blocks.forEach(block => {
		if (event.target.value === block.id)
		{
			block.style.display = "flex";
		}
		else block.style.display = "none";
	});
}

document.addEventListener('DOMContentLoaded', () => {
	// Functions to open and close a modal
	function openModal($el)
	{
		$el.classList.add('is-active');
	}
	function closeModal($el)
	{
		$el.classList.remove('is-active');
	}

	function closeAllModals()
	{
		(document.querySelectorAll('.modal') || []).forEach(($modal) => {
			closeModal($modal);
		});
	}

	// Add a click event on buttons to open a specific modal
	(document.querySelectorAll('.js-modal-trigger') || []).forEach(($trigger) => {
		const modal = $trigger.dataset.target;
		const $target = document.getElementById(modal);

		$trigger.addEventListener('click', () => {
			openModal($target);
		});
	});

	// Add a click event on various child elements to close the parent modal
	(document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(($close) => {
		const $target = $close.closest('.modal');

		$close.addEventListener('click', () => {
			closeModal($target);
		});
	});

	// Add a keyboard event to close all modals
	document.addEventListener('keydown', (event) => {
		const e = event || window.event;

		if (e.keyCode === 27)
		{ // Escape key
			closeAllModals();
		}
	});

	setRenderRangeText();
	const prevBtn = document.getElementById("prevBtn");
	const nextBtn = document.getElementById("nextBtn");
	const todayBtn = document.getElementById("todayBtn");

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