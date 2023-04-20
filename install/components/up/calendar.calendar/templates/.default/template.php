<?php
/**
 * @var $arResult
 */
\Bitrix\Main\UI\Extension::load('up.schedule');
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<div class="tabs is-right">
	<ul>
		<li class="tab" id="day" onclick="changeView(event, 'day')"><a>День</a></li>
		<li class="tab" id="week" onclick="changeView(event, 'week')"><a>Неделя</a></li>
		<li class="tab is-active" id="month" onclick="changeView(event, 'month')"><a>Месяц</a></li>
	</ul>
</div>

<div id="calendar" style="height: 800px">
</div>

<script>
	BX.ready(function() {
		window.CalendarEventsList = new BX.Up.Calendar.Schedule({
			idTeam: <?= json_encode($arResult, JSON_THROW_ON_ERROR) ?>,
			rootNodeId: 'calendar',
			idCalendar: 'user'
		});
	});
</script>