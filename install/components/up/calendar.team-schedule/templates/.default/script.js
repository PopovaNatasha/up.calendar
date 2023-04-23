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

// const prevBtn = document.getElementById("prevBtn");
// const nextBtn = document.getElementById("nextBtn");
// prevBtn.addEventListener("click", e => {
// 	CalendarEventsList.calendar.prev();
// });
//
// nextBtn.addEventListener("click", e => {
// 	CalendarEventsList.calendar.next();
// });

function display_usgs_change() {
	if(event.target.value === 'daily') {
		document.getElementById('every').style.display = "block";
		document.getElementById('day_count').style.display = "block";
		document.getElementById('day').style.display = "block";
	}
	else {
		document.getElementById('every').style.display = "none";
		document.getElementById('day_count').style.display = "none";
		document.getElementById('day').style.display = "none";
	}
}

document.addEventListener('DOMContentLoaded', () => {
	// Functions to open and close a modal
	function openModal($el)
	{
		$el.classList.add('is-active');
		// let select = document.getElementById('select-repeat');
		//
		// select.addEventListener('change', function() {
		// 	switch (select.value) {
		// 		case 'daily':
		//
		// 	}
		// })
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

	const prevBtn = document.getElementById("prevBtn");
	const nextBtn = document.getElementById("nextBtn");
	prevBtn.addEventListener("click", e => {
		CalendarEventsList.calendar.prev();
	});

	nextBtn.addEventListener("click", e => {
		CalendarEventsList.calendar.next();
	});

});