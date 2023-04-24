<?php
/**
 * @var $arResult
 */
\Bitrix\Main\UI\Extension::load('up.schedule');
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<div class="box">
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
	BX.ready(function() {
		window.CalendarEventsList = new BX.Up.Calendar.Schedule({
			idTeam: <?= json_encode($arResult, JSON_THROW_ON_ERROR) ?>,
			rootNodeId: 'calendar',
			isUser: true,

		});
	});
</script>