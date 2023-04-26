<?php
/**
 * @var array $arResult
 */
use Bitrix\Main\UI\Extension;

Extension::load('up.schedule');
var_dump($arResult);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<div class="box columns">

	<div class="column is-2 calendar">
		<div class="tabs is-left">
			<ul style="margin-left: 0">
				<span style="padding: 0.5em 1em;">Группы</span>
			</ul>
		</div>

		<div class="sidebar">
			<?php foreach ($arResult['teams'] as $team):?>
				<div class="field">
					<label class="calendars-team">
						<input type="checkbox" class="checkbox-round" value="<?= $team['ID_TEAM'] ?>" id="chbox-<?= $team['ID_TEAM'] ?>" checked
							   onfocus="this.blur()"
							   style="
								   border:<?= $team['COLOR'] ? '2px solid #' . $team['COLOR'] : '2px solid #a1b56c'?>;

								   ">
						<span class="team-title"><?= $team['TITLE'] ?></span>
					</label>
				</div>
			<?php endforeach; ?>
			<button class="button js-modal-trigger" data-target="modal-js-teamColor">Редактировать</i></button>
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

<form name="change-team-color" method="post">
	<div class="modal" id="modal-js-teamColor" >
		<div class="modal-background"></div>
		<div class="modal-card">
			<header class="modal-card-head">
				<p class="modal-card-title">Выбор цвета</p>
				<button class="delete" type="reset" aria-label="close"></button>
			</header>

			<section class="modal-card-body">
				<div class="control">
					<div class="select is-primary">
						<select id="selectTeam" name="idTeam" onchange="displayColorTeam()">
							<?php foreach ($arResult['teams'] as $team): ?>
								<option value="<?= $team['ID_TEAM'] ?>"><?= $team['TITLE'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<?php foreach ($arResult['teams'] as $key => $team): ?>
					<div class="control team" id="<?= $team['ID_TEAM'] ?>" style="display:<?= ($key === 0) ? 'flex' : 'none'?>">
						<input hidden name="id-team" value="<?= $team['ID_TEAM'] ?>" >
						<input type="text"name="title" class="input is-primary mb-4" value="<?= $team['TITLE'] ?>" readonly required>
						<input type="color" name="color" class="input is-primary mb-4 team-color" value="#a1b56c">
					</div>
				<?php endforeach; ?>
			</section>

			<footer class="modal-card-foot">
				<button class="button is-success" type="submit">Сохранить</button>
				<button class="button" type="reset" >Отмена</button>
			</footer>
		</div>
	</div>
</form>

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