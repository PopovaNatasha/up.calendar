<?php
/**
 * @var $arResult
 * @var $USER
 */

use Bitrix\Main\UI\Extension;

Extension::load('up.schedule');
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
                                <?= \CFile::ShowImage($arResult['PERSONAL_PHOTO'], 256, 256) ?>
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
                    <div class="box"
                         style="display: flex; flex-direction: column; align-items: center; margin-bottom: 20px">
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
                            <button class="button is-primary is-light js-modal-trigger"
                                    data-target="modal-js-leave-team" style="margin-left: auto">Покинуть
                            </button>
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
                        <button class="button is-primary js-modal-trigger" data-target="modal-js-crateEvent">Создать
                            событие
                        </button>
                        <button class="button is-primary js-modal-trigger" data-target="modal-js-invite">Пригласить
                        </button>
                        <button class="button is-primary js-modal-trigger" data-target="modal-js-settings">Настройки
                        </button>
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
                        <input id="titleEvent" name="title" class="input is-primary mb-4" type="text" required>
                    </div>
                    <div class="field">
                        <label>Время</label>
                        <div class="control">
                            <input name="date" type="date" class="input is-primary mb-4" id="date" required>
                        </div>
                    </div>
                    <div class="field">
                        <label>Повторяемость</label>
                        <div class="columns create-event">
                            <div class="column control">
                                <div class="select is-primary">
                                    <select id="selectRepeat" name="rule_repeat" onchange="displayUsgsChange('create')">
                                        <option value="non">Не повторяется</option>
                                        <option value="daily">Каждый день</option>
                                        <option value="weekly">Каждую неделю</option>
                                    </select>
                                </div>
                            </div>
                            <div class="column is-2" id="create-every">
                                <span class="rule-daily">каждый</span>
                            </div>
                            <div class="column control is-2" id="create-day-count">
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
                            <div class="column" id="create-day">
                                <span class="rule-daily">день</span>
                            </div>
                        </div>
                </section>
                <footer class="modal-card-foot">
                    <button id="buttonSaveEvent" class="button is-success" type="submit">Сохранить</button>
                    <button class="button is-warning" type="reset">Cброс</button>
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

    <div class="modal" id="modal-js-invite">
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
                        <input id="InviteLink" name="title" class="input is-primary mb-4 is-large" type="text" readonly
                               value="<?= $_SERVER['HTTP_HOST'] ?>/invite/<?= $arResult['INVITE_LINK'] ?>/">
                    </div>
                    <button class="button is-primary"
                            onclick="generateLink('<?= $_SERVER['HTTP_HOST'] ?>/invite/',<?= $arResult['ID'] ?>)">
                        Создать новую ссылку
                    </button>
                </div>
            </section>
            <footer class="modal-card-foot">
                <button class="button" type="reset">Закрыть</button>
            </footer>
        </div>
    </div>

    <form name="settings" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="settings" value="1"/>
        <div class="modal" id="modal-js-settings">
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
                            <input name="title" class="input is-primary mb-4 is-large" type="text"
                                   value="<?= htmlspecialchars($arResult['TITLE']) ?>" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Описание</label>
                        <div class="control">
                            <input name="description" class="input is-primary mb-4 " type="text"
                                   value="<?= htmlspecialchars($arResult['DESCRIPTION']) ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Изображение</label>
                        <div class="control">
                            <input name="img" class="input is-primary mb-4 " type="file" accept="image/*">
                        </div>
                    </div>
                    <label class="checkbox">
                        <input name="isPrivate"
                               type="checkbox" <?= ($arResult['IS_PRIVATE'] === '0') ? 'checked' : '' ?>>
                        Публичная группа
                    </label>
                </section>
                <footer class="modal-card-foot">
                    <button class="button is-success" type="submit">Сохранить</button>
                    <button class="button" type="reset">Отмена</button>
                </footer>
            </div>
        </div>
    </form>

    <form name="confirmation" method="post">
        <input type="hidden" name="action" value="out"/>
        <div class="modal" id="modal-js-leave-team">
            <div class="modal-background"></div>
            <div class="modal-card leave-team">
                <header class="modal-card-head" style="border: none">
                    <p class="modal-card-title">Подтверждение</p>
                    <button class="delete" type="reset" aria-label="close"></button>
                </header>
                <section class="modal-card-body" style="background-color: #f5f5f5; ">
                    <p>Вы уверены, что хотите покинуть группу?</p>
                </section>
                <footer class="modal-card-foot" style="border: none">
                    <button class="button is-danger" type="submit">Покинуть</button>
                    <button class="button" type="reset">Остаться</button>
                </footer>
            </div>
        </div>
    </form>

    <div role="dialog" class="toastui-calendar-popup-container" id="event-detail-popup">
        <div class="toastui-calendar-detail-container">
            <div class="toastui-calendar-popup-section toastui-calendar-section-header">
                <div class="toastui-calendar-event-title">
                    <span class="toastui-calendar-template-popupDetailTitle" id="popupDetailTitle">Пленэр в Ботаническом саду</span>
                </div>
                <div class="toastui-calendar-content">
                    <span class="toastui-calendar-template-popupDetailDate" id="popupDetailDate">2023.04.27 10:00 am - 11:59 am</span>
                </div>
            </div>
            <div class="toastui-calendar-popup-section toastui-calendar-section-detail">
                <div class="toastui-calendar-detail-item">
                    <span class="toastui-calendar-icon toastui-calendar-ic-repeat-b"></span>
                    <span class="toastui-calendar-content">
					<span class="toastui-calendar-template-popupDetailRecurrenceRule" id="popupDetailRecurrenceRule">каждые 7 дней</span>
				</span>
                </div>
            </div>
            <div class="toastui-calendar-popup-section toastui-calendar-section-button">
                <button type="button" class="toastui-calendar-edit-button">
                    <span class="toastui-calendar-icon toastui-calendar-ic-edit"></span>
                    <span class="toastui-calendar-content">
					<span class="toastui-calendar-template-popupEdit js-modal-trigger"
                          data-target="modal-js-changeEvent">Изменить</span>
				</span>
                </button>
                <div class="toastui-calendar-vertical-line"></div>
                <button type="button" class="toastui-calendar-delete-button">
                    <span class="toastui-calendar-icon toastui-calendar-ic-delete"></span>
                    <span class="toastui-calendar-content js-modal-trigger"
						  data-target="modal-js-deleteEvent">Удалить</span>
                    </span>
                </button>
            </div>
        </div>
        <div class="toastui-calendar-popup-top-line" id="popupTopLine"
             style="background-color: rgb(131, 109, 182);"></div>
        <div class="toastui-calendar-popup-arrow toastui-calendar-left">
            <div class="toastui-calendar-popup-arrow-border" style="top: 94px;">
                <div class="toastui-calendar-popup-arrow-fill"></div>
            </div>
        </div>
    </div>


    <div class="modal" id="modal-js-changeEvent">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Изменить событие</p>
                <button class="delete" type="reset" aria-label="close"></button>
            </header>
            <section class="modal-card-body">
                <label>Название cобытия</label>
                <div class="control">
                    <input name="id" type="hidden" id="popupChangeEventId" required>
                    <input name="title" class="input is-primary mb-4" type="text" id="popupChangeTitle" required>
                </div>
                <div class="field">
                    <label>Время</label>
                    <div class="control">
                        <input name="date" type="date" class="input is-primary mb-4" id="date">
                    </div>
                </div>
                <div class="field">
                    <label class="checkbox" id="checkboxIsAllLabel">
                        <input name="isAll" type="checkbox" id="checkboxIsAll">
                        Изменить все
                    </label>
                </div>
                <div class="field" id="changeRepeat">
                    <label>Повторяемость</label>
                    <div class="columns change-event">
                        <div class="column control">
                            <div class="select is-primary">
                                <select id="changeSelectRepeat" name="rule_repeat"
                                        onchange="displayUsgsChange('change')">
                                    <option value="daily">Каждый день</option>
                                    <option value="weekly">Каждую неделю</option>
                                </select>
                            </div>
                        </div>
                        <div class="column is-2" id="change-every">
                            <span class="rule-daily">каждый</span>
                        </div>
                        <div class="column control is-2" id="change-day-count">
                            <div class="select is-primary">
                                <select name="rule_repeat_count" id="changeSelectCount">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>
                        </div>
                        <div class="column" id="change-day">
                            <span class="rule-daily">день</span>
                        </div>
                    </div>
                </div>
            </section>
            <footer class="modal-card-foot">
                <button class="button is-success" onclick="CalendarEventsList.changeEvent()">Сохранить</button>
                <button class="button" type="reset">Отмена</button>
            </footer>
        </div>
    </div>

	<div class="modal" id="modal-js-deleteEvent">
		<div class="modal-background"></div>
		<div class="modal-card delete-event">
			<header class="modal-card-head" style="border: none">
				<p class="modal-card-title">Удалить событие</p>
				<button class="delete" type="reset" aria-label="close"></button>
			</header>
			<section class="modal-card-body" style="background-color: #f5f5f5; ">
				<p>Вы уверены, что хотите удалить это событие?</p>
				<div class="field">
					<label class="checkbox" id="checkboxDeleteIsAllLabel">
						<input name="isAll" type="checkbox" id="checkboxDeleteIsAll">
						Удалить все
					</label>
				</div>
			</section>
			<footer class="modal-card-foot" style="border: none">
				<button class="button is-danger" onclick="CalendarEventsList.deleteEvent()">Удалить</button>
				<button class="button" type="reset">Отмена</button>
			</footer>
		</div>
	</div>

</section>

<script>
    var EventDatePickers = bulmaCalendar.attach('#date', {
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
    BX.ready(function () {
        window.CalendarEventsList = new BX.Up.Calendar.Schedule({
            idTeam: <?= json_encode($arResult['ID'], JSON_THROW_ON_ERROR) ?>,
            rootNodeId: 'calendar',
            isUser: false,
        });
    });
</script>