<?php

/**
 * @var $arResult
 */
\Bitrix\Main\UI\Extension::load('up.schedule');

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
<script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
<link href="https://unpkg.com/bulma-calendar@6.0.7/dist/css/bulma-calendar.min.css" rel="stylesheet">
<script src="https://unpkg.com/bulma-calendar@6.0.7/dist/js/bulma-calendar.min.js"></script>
<script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>


<?php //if ($USER->getID() === $arResult['ID_ADMIN']): ?>
	<div class="buttons admin">
		<button class="button is-primary js-modal-trigger" data-target="modal-js-crateEvent">Создать событие</button>
	</div>
<?php //endif ?>

<form name="create-event" method="post">
	<div class="modal" id="modal-js-crateEvent" >
		<div class="modal-background"></div>
		<div class="modal-card">
			<header class="modal-card-head">
				<p class="modal-card-title">Новое событие</p>
				<button class="delete" type="reset" aria-label="close"></button>
			</header>

			<section class="modal-card-body">
				<label>Название cобытия</label>
				<div class="control">
					<input name="title" class="input is-primary mb-4" type="text" required>
				</div>
				<div class="field">
					<label>Время</label>
					<div class="control">
						<input name="date" type="date" class="input is-primary mb-4" id="date">
					</div>
				</div>

				<div class="field">
					<label>Повторяемость</label>
					<div class="columns">

						<div class="column control">
							<div class="select is-primary">
								<select name="rule_repeat">
									<option value="non">Не повторяется</option>
									<option value="daily">Каждый день</option>
									<option value="weekly">Каждую неделю</option>
<!--									<option value="monthly">Каждый месяц</option>-->
								</select>
							</div>
						</div>

						<div class="column">
							<span class="rule-daily">каждый</span>
							<span class="rule-weekly">каждую</span>
<!--							<span class="rule-monthly">каждый</span>-->
						</div>

						<div class="column control">
							<div class="select is-primary">
								<select name="rule_repeat_count">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
								</select>
							</div>
						</div>

						<div class="column">
							<span class="rule-daily">день</span>
							<span class="rule-weekly">неделю</span>
							<span class="rule-monthly">месяц</span>
						</div>
					</div>

<!--					<div class="week-day columns">-->
<!--						<label class="column"><input name="MO" type="checkbox" value="MO">Пн</label>-->
<!--						<label class="column"><input name="TU" type="checkbox" value="TU">Вт</label>-->
<!--						<label class="column"><input name="day-of-week" type="checkbox" value="WE">Ср</label>-->
<!--						<label class="column"><input name="day-of-week" type="checkbox" value="TH">Чт</label>-->
<!--						<label class="column"><input name="day-of-week" type="checkbox" value="FR">Пт</label>-->
<!--						<label class="column"><input name="day-of-week" type="checkbox" value="SA">Сб</label>-->
<!--						<label class="column"><input name="day-of-week" type="checkbox" value="SU">Вс</label>-->
<!--					</div>-->
				</div>

			</section>
			<footer class="modal-card-foot">
				<button class="button is-success" type="submit">Сохранить</button>
				<button class="button" type="reset" >Отмена</button>
			</footer>
		</div>
	</div>
</form>

<div class="tabs is-right">
	<ul>
		<li class="tab" onclick="changeView(event, 'day')"><a >День</a></li>
		<li class="tab" onclick="changeView(event, 'week')"><a >Неделя</a></li>
		<li class="tab is-active" onclick="changeView(event, 'month')"><a >Месяц</a></li>
	</ul>
</div>

<div id="calendar" style="height: 800px">
	<script>
		const Calendar = tui.Calendar;
		const calendar = new Calendar('#calendar', {
			// Whether to use the default form popup when creating/modifying events.
			useFormPopup: false,
			// Whether to use the default detail popup when clicking events.
			useDetailPopup: false,
			// whether use default creation popup or not
			useCreationPopup: false,
			// 'day', 'week', 'month'
			defaultView: 'month',
			// shows the milestone and task in weekly, daily view
			taskView: false,
			// shows the all day and time grid in weekly, daily view
			scheduleView: false,
			// template options
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
					return schedule.title + ' <i class="fa fa-refresh"></i>' + schedule.start;
				}
			},

			week: {
				dayNames: ['Вск', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
				startDayOfWeek: 1,
				narrowWeekend: false,
				taskView: false,
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
		calendar.createEvents([
			{
				id: 'event1',
				calendarId: 'cal2',
				title: 'Weekly meeting',
				start: '2023-04-18T09:00:00',
				end: '2023-04-18T10:00:00',
			},
			{
				id: 'event2',
				calendarId: 'cal1',
				title: 'Lunch appointment',
				start: '2023-04-19T12:00:00',
				end: '2023-04-19T13:00:00',
			},
			{
				id: 'event3',
				calendarId: 'cal2',
				title: 'Vacation',
				start: '2023-04-20',
				end: '2023-04-20',
				isAllday: true,
				category: 'allday',
			},
		]);
		const timedEvent = calendar.getEvent('1', 'cal1'); // EventObject
		calendar.on('clickEvent', ({ event }) => {
			console.log(event); // EventObject
		});
	</script>
</div>

<script>
	var calendars = bulmaCalendar.attach('#date', {
		type: 'datetime',
		startDate: new Date(),
		displayMode: 'dialog',
		showHeader: false,
		// headerPosition: 'top',
		showFooter: true,
		showButtons: true,
		showTodayButton: true,
		showClearButton: true,
		validateLabel: 'Input',
		isRange: true,
		// allowSameDayRange: true,
	});

	// Loop on each calendar initialized
	for(var i = 0; i < calendars.length; i++) {
		// Add listener to date:selected event
		calendars[i].on('select', date => {
			console.log(date);
		});
	}

	// To access to bulmaCalendar instance of an element
	var element = document.querySelector('#my-element');
	if (element) {
		// bulmaCalendar instance is available as element.bulmaCalendar
		element.bulmaCalendar.on('select', function(datepicker) {
			console.log(datepicker.data.value());
		});
	}
</script>

<script>
	BX.ready(function() {
		window.CalendarEventsList = new BX.Up.Calendar.Schedule({
			idTeam: '<?= $arResult['idTeam'] ?>',
			rootNodeId: 'calendar',
		});
	});
</script>
