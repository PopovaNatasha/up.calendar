<?php
/**
 * @var array $arResult
 */
use Bitrix\Main\UI\Extension;

Extension::load('up.schedule');

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<div class="box columns">

	<div class="column is-2" style="border-right: #dbdbdb solid 1px">
		<div class="tabs is-left">
			<ul style="margin-left: 0">
				<span style="padding: 0.5em 1em;">Группы</span>
			</ul>
		</div>
		<?php foreach ($arResult['teams'] as $team):?>
			<div class="calendars-team">
				<label>
					<input type="checkbox" class="tui-full-calendar-checkbox-round" value="<?= $team['id'] ?>" checked>
					<span style="border-color: rgb(0, 169, 255); background-color: rgb(0, 169, 255);"></span>
					<span><?= $team['title'] ?></span>
				</label>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="column">
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
</div>

<script>
	BX.ready(function() {
		window.CalendarEventsList = new BX.Up.Calendar.Schedule({
			idTeam: <?= json_encode($arResult['idTeams'], JSON_THROW_ON_ERROR) ?>,
			rootNodeId: 'calendar',
			isUser: true,
		});
	});
</script>