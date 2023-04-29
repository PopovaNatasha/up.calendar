<?php
/**
 * @var $arResult
 * @var $USER
 */
\Bitrix\Main\UI\Extension::load('up.schedule');
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<section class="container">

			<div class="box">
				<div class="columns">
					<div class="column has-text-centered">
						<div class="card">
							<div class="card-image" style="display:flex; justify-content: center;">
								<figure class="image is-256x256">
									<?php if ($arResult['PERSONAL_PHOTO']): ?>
										<?= \CFile::ShowImage($arResult['PERSONAL_PHOTO'], 256, 256)?>
									<?php else: ?>
										<img src="https://bulma.io/images/placeholders/256x256.png" alt="Placeholder image">
									<?php endif; ?>
								</figure>
							</div>

						</div>
					</div>
					<div class="column is-9" style="display: flex">
						<div style="display: flex; justify-content: space-between;">
						<div style="display: flex;flex-direction: column;">
						<h1 class="title is-4"><?= htmlspecialchars($arResult['TITLE']) ?></h1>
						<p><?= htmlspecialchars($arResult['DESCRIPTION']) ?></p>
						</div>
							<div class="box" style="display: flex; flex-direction: column; align-items: center; margin-bottom: 20px">
							<p class="description">Участников:</p>
							<p class="is-left"><?= count($arResult['PARTICIPANTS']) ?></p>
							</div>
						</div>
						<?php if ($USER->getID() !== $arResult['ID_ADMIN']): ?>
							<?php foreach ($arResult['PARTICIPANTS'] as $participant): ?>
								<?php $result .= in_array($USER->getID(), $participant, true); ?>
							<?php endforeach; ?>
							<?php if ($result): ?>
								<div class="buttons">
									<button class="button is-primary is-light js-modal-trigger" data-target="modal-js-leave-team" style="margin-left: auto">Покинуть</button>
								</div>
							<?php else: ?>
								<form class="buttons" method="post">
									<input type="hidden" name="action" value="in"/>
									<button class="button is-primary is-light" style="margin-left: auto">Вcтупить</button>
								</form>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($USER->getID() === $arResult['ID_ADMIN']): ?>
							<div class="buttons admin is-right">
								<button class="button is-primary js-modal-trigger" data-target="modal-js-crateEvent">Создать событие</button>
								<button class="button is-primary js-modal-trigger" data-target="modal-js-example1">Пригласить</button>
								<button class="button is-primary js-modal-trigger" data-target="modal-js-example2">Настройки</button>
							</div>
						<?php endif; ?>
					</div>
			</div>
		</div>





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

		<div class="box is-9">

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
				minuteSteps: 1,
			});

		</script>

		<script>
			BX.ready(function() {
				window.CalendarEventsList = new BX.Up.Calendar.Schedule({
					idTeam: <?= json_encode($arResult['ID'], JSON_THROW_ON_ERROR) ?>,
					rootNodeId: 'calendar',
					isUser: false,
				});
			});
		</script>

				<div class="modal" id="modal-js-example1" >
					<div class="modal-background"></div>
					<div class="modal-card">
						<header class="modal-card-head">
							<p class="modal-card-title">Ссылка для приглашения</p>
							<button class="delete" type="reset" aria-label="close"></button>
						</header>

						<section class="modal-card-body">
							<div class="field">
								<label class="label">Ссылка</label>
								<div class="control">
									<input id="InviteLink" name="title" class="input is-primary mb-4 is-large" type="text" readonly value="<?= $_SERVER['HTTP_HOST']?>/invite/<?=$arResult['INVITE_LINK']?>/">
								</div>
								<button class="button is-primary" onclick="generateLink('<?= $_SERVER['HTTP_HOST']?>/invite/',<?= $arResult['ID'] ?>)">Создать новую ссылку</button>
							</div>

						</section>
						<footer class="modal-card-foot">
							<button class="button" type="reset" >Закрыть</button>
						</footer>
					</div>
				</div>


			<form name="settings" action="" method="post" enctype="multipart/form-data">
				<input type="hidden" name="settings" value="1"/>
				<div class="modal" id="modal-js-example2" >
					<div class="modal-background"></div>
					<div class="modal-card">
						<header class="modal-card-head">
							<p class="modal-card-title">Редактирование группы</p>
							<button class="delete" type="reset" aria-label="close"></button>
						</header>

						<section class="modal-card-body">
							<div class="field">
								<label class="label">Название</label>
								<div class="control">
									<input name="title" class="input is-primary mb-4 is-large" type="text" value="<?= htmlspecialchars($arResult['TITLE']) ?>" required>
								</div>
							</div>
							<div class="field">
								<label class="label">Описание</label>
								<div class="control">
									<input name="description" class="input is-primary mb-4 " type="text" value="<?= htmlspecialchars($arResult['DESCRIPTION']) ?>">
								</div>
							</div>
							<div class="field">
								<label class="label">Изображение</label>
								<div class="control">
									<input name="img" class="input is-primary mb-4 " type="file" accept="image/*">
								</div>
							</div>
							<label class="checkbox">
								<input name="isPrivate" type="checkbox" <?= ($arResult['IS_PRIVATE'] === '0') ? 'checked' : '' ?>>
								Публичная группа
							</label>

						</section>
						<footer class="modal-card-foot">
							<button class="button is-success" type="submit">Сохранить</button>
							<button class="button" type="reset" >Отмена</button>
						</footer>
					</div>
				</div>
			</form>

			<form name="confirmation" action="" method="post">
				<input type="hidden" name="action" value="out"/>
				<div class="modal" id="modal-js-leave-team">
					<div class="modal-background"></div>
					<div class="modal-card">
						<header class="modal-card-head" style="border: none">
							<p class="modal-card-title">Подтверждение</p>
							<button class="delete" type="reset" aria-label="close"></button>
						</header>
						<section class="modal-card-body" style="background-color: #f5f5f5; ">
							<p>Вы уверены, что хотите покинуть группу?</p>
						</section>
						<footer class="modal-card-foot" style="border: none">
							<button class="button is-danger" type="submit">Покинуть</button>
							<button class="button" type="reset" >Отмена</button>
						</footer>
					</div>
				</div>
			</form>
</section>

<script>
	bulmaCalendar.attach('#date', {
		dateFormat: "DD-MMM-YYYY",
		type: 'date',
		showClearButton: false
	});
	document.querySelector('#date').bulmaCalendar.on('select', date => {console.log(date)});

</script>

<script>

	const MONTH_STRING_EN = [
		'Jan',
		'Feb',
		'Mar',
		'Apr',
		'May',
		'Jun',
		'Jul',
		'Aug',
		'Sep',
		'Oct',
		'Nov',
		'Dec'
	]
	var dateInput = document.getElementsByClassName('date-input')[0]

	renderDates();
	dateInput.addEventListener('change', console.log('ggg'))

	function renderDates(){
		var today
		if(dateInput.value)
			today = new Date(dateInput.value)
		else
			today = new Date()

		var year = today.getFullYear();
		var month = today.getMonth();
		var daysOfMonth = getDaysOfMonth(today.getFullYear(), today.getMonth())

		var daysDom = Array.apply(null, {length: daysOfMonth}).map(function(_, i){
			var class_string = '';
			if (i + 1 === today.getDate())
				class_string = 'date-item current';
			else
				class_string = 'date-item'
			return "<span class='" + class_string + "'>" + (i+1) + "</span>"
		}).join('')



		document.getElementsByClassName('dates')[0].innerHTML = daysDom

		document.getElementsByClassName('year-month')[0].innerHTML = MONTH_STRING_EN[today.getMonth()] + " " + today.getFullYear();

		document.getElementsByClassName('dates')[0].addEventListener('click', changeDate.bind({dateInput, year, month}))



	}

	function changeDate(e) {
		var date = this.year + '/' + (this.month + 1) + '/' + e.target.textContent;
		this.dateInput.value = date;
		renderDates();
	}

	function createDomElement(type, props, child){
		var element = document.createElement(type);

		Object.keys(props).forEach(function (key) {
			element.setAttribute(key, props[key]);
		});

		switch (typeof child) {
			case 'number':
			case 'string':
				element.innerHTML = child;
				break;
			case 'object':
				element.innerHTML = child.join('');
				break;
		}

		return element;
	}

	function getDaysOfMonth (year, month) {
		return [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
	}

</script>