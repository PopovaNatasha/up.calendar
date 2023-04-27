<?php
/**
 * @var array $arResult
 * @var $USER
 */
\Bitrix\Main\UI\Extension::load('up.schedule');
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<form name="create-event" method="post">
	<div class="modal" id="modal-js-crateEvent">
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
								<select id="selectRepeat" name="rule_repeat" onchange="displayUsgsChange()">
									<option value="non">Не повторяется</option>
									<option value="daily">Каждый день</option>
									<option value="weekly">Каждую неделю</option>
								</select>
							</div>
						</div>
						<div class="column" id="every">
							<span class="rule-daily">каждый</span>
						</div>
						<div class="column control" id="day_count">
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
						<div class="column" id="day">
							<span class="rule-daily">день</span>
						</div>
					</div>
			</section>
			<footer class="modal-card-foot">
				<button class="button is-success" type="submit">Сохранить</button>
				<button class="button" type="reset">Отмена</button>
			</footer>
		</div>
	</div>
</form>

<div class="box">
	<?php if ($USER->getID() === $arResult['team']['ID_ADMIN']): ?>
		<div class="buttons admin">
			<button class="button is-primary js-modal-trigger" data-target="modal-js-crateEvent">Создать событие</button>
		</div>
	<?php endif; ?>
	<div class="tabs is-right" style="align-items: flex-end">
		<div>
			<button class="button " id="prevBtn"><i class="fa-solid fa-chevron-left"></i></button>
			<button class="button " id="todayBtn">Сегодня</i></button>
			<button class="button " id="nextBtn"><i class="fa-solid fa-chevron-right"></i></button>
		</div>
		<ul>
			<span id="renderRange" style="margin-right: auto"></span>
			<li class="tab" onclick="changeView(event, 'day')"><a>День</a></li>
			<li class="tab" onclick="changeView(event, 'week')"><a>Неделя</a></li>
			<li class="tab is-active" onclick="changeView(event, 'month')"><a>Месяц</a></li>
		</ul>
	</div>
	<div id="calendar" style="height: 800px">
	</div>
</div>

<script>
	var calendars = bulmaCalendar.attach('#date', {
		type: 'datetime',
		startDate: new Date(),
		displayMode: 'dialog',
		showHeader: false,
		validateLabel: 'Ввод',
		cancelLabel: 'Выход',
		clearLabel: 'Очистить',
		todayLabel: 'Сегодня',
		isRange: true,
		dateFormat: 'DD.MM.YYYY',
		weekStart: 1,
	});

	// // Loop on each calendar initialized
	// for (var i = 0; i < calendars.length; i++)
	// {
	// 	// Add listener to date:selected event
	// 	calendars[i].on('select', date => {
	// 		console.log(date);
	// 	});
	// }
	//
	// // To access to bulmaCalendar instance of an element
	// var element = document.querySelector('#my-element');
	// if (element)
	// {
	// 	// bulmaCalendar instance is available as element.bulmaCalendar
	// 	element.bulmaCalendar.on('select', function(datepicker) {
	// 		console.log(datepicker.data.value());
	// 	});
	// }
</script>

<script>
	BX.ready(function() {
		window.CalendarEventsList = new BX.Up.Calendar.Schedule({
			idTeam: <?= json_encode($arResult['teamId'], JSON_THROW_ON_ERROR) ?>,
			rootNodeId: 'calendar',
			isUser: false,
		});
	});
</script>