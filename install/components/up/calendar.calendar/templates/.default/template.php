<?php
/**
 * @var array $arResult
 */

use Bitrix\Main\UI\Extension;

Extension::load('up.schedule');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}
?>

<div class="box columns">

	<div class="column is-2 calendar">
		<div class="tabs is-left">
			<ul style="margin-left: 0">
				<span style="padding: 0.5em 1em;">Группы</span>
			</ul>
		</div>
		<div class="sidebar">
			<?php foreach ($arResult['teams'] as $team): ?>
				<div class="field">
					<label class="calendars-team">
						<input type="checkbox" class="checkbox-round" value="<?= $team['ID_TEAM'] ?>" id="chbox-<?= $team['ID_TEAM'] ?>" checked
							   onfocus="this.blur()"
							   style="border:<?= $team['COLOR'] ? '2px solid ' . $team['COLOR']
								   : '2px solid #a1b56c' ?>;">
						<span class="team-title"><?= $team['TITLE'] ?></span>
						<button class="button is-small change-color" id="<?= $team['ID_TEAM'] ?>" data-title="<?= $team['TITLE'] ?>" data-color="<?= $team['COLOR'] ?>">
							<span class="icon is-small">
								<i class="fa-solid fa-pen"></i>
							</span>
						</button>
					</label>
				</div>
			<?php endforeach; ?>
		</div>
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

<div class="form-popup" id="change-color-form">
	<article class="message">
		<form class="message-body" name="change-team-color" method="post">
			<label class="input-team-color">
				<input id="input-id" name="id" hidden>
				<input id="input-title" class="input is-small" type="text" name="title" readonly>
				<input id="input-color" class="input is-small team-color" type="color" name="color">
			</label>
			<div class="buttons">
				<button class="button is-primary is-small" type="submit">Сохранить</button>
				<button class="button is-small" type="reset" id="close-button">Отмена</button>
			</div>
		</form>
	</article>
</div>

<script>
	BX.ready(function() {
		window.CalendarEventsList = new BX.Up.Calendar.Schedule({
			idTeam: <?= json_encode($arResult['idTeams'], JSON_THROW_ON_ERROR) ?>,
			rootNodeId: 'calendar',
			teams: <?= json_encode($arResult['teams'], JSON_THROW_ON_ERROR) ?>,
			isUser: true,
		});
	});
</script>