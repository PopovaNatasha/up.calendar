<?php
/**
 * @var $arResult
 * @var $USER
 */
?>
<section class="container">

<div class="columns is-multiline">

	<div class="column has-text-centered">

		<div class="card">
			<div class="card-image">
				<figure class="image is-4by3">
					<img src="https://bulma.io/images/placeholders/1280x960.png" alt="Placeholder image">
				</figure>
			</div>
			<div class="card-content">
				<p class="description">Подписчиков</p>
				<h1 class="title is-4">34</h1>
			</div>
		</div>
	</div>

	<div class="column is-half">
		<div class="box">
			<h1 class="title is-4"><?= $arResult['TITLE'] ?></h1>
			<p><?= $arResult['DESCRIPTION'] ?></p>

			<?php if ($USER->getID() !== $arResult['ID_ADMIN']): ?>
				<?php foreach ($arResult['PARTICIPANTS'] as $participant): ?>
					<?php $result .= in_array($USER->getID(), $participant, true); ?>
				<?php endforeach; ?>
				<?php if ($result): ?>
					<form class="buttons" method="post">
						<input type="hidden" name="idTeam" value="<?= $arResult['ID'] ?>"/>
						<input type="hidden" name="action" value="out"/>
						<button class="button is-primary is-light" style="margin-left: auto">Покинуть</button>
					</form>
				<?php else: ?>
					<form class="buttons" method="post">
						<input type="hidden" name="idTeam" value="<?= $arResult['ID'] ?>"/>
						<input type="hidden" name="action" value="in"/>
						<button class="button is-primary is-light" style="margin-left: auto">Вcтупить</button>
					</form>
				<?php endif; ?>
			<?php endif; ?>

		</div>

		<form class="box">
			<div class="field">
				<div class="control">
					<textarea class="textarea" placeholder="Введите сообщение" rows="2"></textarea>
				</div>
			</div>
			<button class="button is-primary is-light">Отправить</button>
		</form>
	</div>

	<div class="column">

		<div class="box">
		<input type="text" class='date-input'>
			<div class="wrapper">
				<div class="level header is-marginless">
					<span class="left-arrow"><</span>
					<span class="year-month"></span>
					<span class="right-arrow">></span>
				</div>

				<div class="date-wrapper">
					<div class="level-left day-nums">
						<span class='date-item'>Sun</span>
						<span class='date-item'>Mon</span>
						<span class='date-item'>Tue</span>
						<span class='date-item'>Wen</span>
						<span class='date-item'>Thu</span>
						<span class='date-item'>Fri</span>
						<span class='date-item'>Sat</span>
					</div>
					<div class="dates">
					</div>
				</div>
			</div>
		</div>

		<?php if ($USER->getID() === $arResult['ID_ADMIN']): ?>
			<div class="buttons admin">
				<button class="button is-primary js-modal-trigger" data-target="modal-js-example">Пригласить</button>
				<button class="button is-primary">Настройки</button>
			</div>
		<?php endif ?>

		<div>
			<div class="modal" id="modal-js-example" >
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
								<input name="title" class="input is-primary mb-4 is-large" type="text" readonly value="Link">
							</div>
						</div>

					</section>
					<footer class="modal-card-foot">
						<button class="button" type="reset" >Закрыть</button>
					</footer>
				</div>
			</div>
		</div>

	</div>
</div>
</section>

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