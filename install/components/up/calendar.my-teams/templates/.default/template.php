<?php

/**
 * @var object $USER
 * @var array $arResult
 */

use Up\Calendar\Services\FlashMessage;

?>

<?php if (FlashMessage::hasError()): ?>
	<pre style="color: red; font-size: large">
			<?= FlashMessage::showMessages() ?>
			<?php FlashMessage::unset(); ?>
		</pre>
<?php endif; ?>

<div class="columns is-mobile">
    <div class="column is-8">
        <form action="/groups/my/?" method="get">
            <input class="input is-primary" type="text" placeholder="Поиск по группам" name="query"
                   value="<?= htmlspecialchars($_GET['query']) ?>">
        </form>
    </div>
    <div class="column ">
        <button class="js-modal-trigger button is-primary" data-target="modal-js-example">
            Создать группу
        </button>
    </div>
</div>

<div class="block">
    <?php foreach ($arResult['Teams'] as $team): ?>
        <a class="group-card block"
           href="/group/<?= htmlspecialchars($team['ID']) ?>/">
            <div class="block group">
                <div>
                    <figure class="image is-64x64">
                        <?php if ($team['PERSONAL_PHOTO']): ?>
							<?php $renderImage = \CFile::ResizeImageGet($team['PERSONAL_PHOTO'], ["width" => 64, "height" => 64], BX_RESIZE_IMAGE_EXACT);?>
							<?= \CFile::ShowImage($renderImage['src'], 64, 64) ?>
                        <?php else: ?>
                            <img src="https://bulma.io/images/placeholders/64x64.png" alt="Placeholder image">
                        <?php endif; ?>
                    </figure>
                </div>
				<div class="teams-title">
					<p style="font-size: large; margin-bottom: 0 !important;" ><?= htmlspecialchars($team['TITLE']) ?></p>
					<?php if ($team['ID_ADMIN'] === $USER->getID()): ?>
						<div class="box admin-mark">ADMIN</div>
					<?php endif; ?>
				</div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<form name="create-team" action="/groups/my/create/" method="post">
	<?=bitrix_sessid_post()?>
    <div class="modal" id="modal-js-example">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Создание группы</p>
                <button class="delete" type="reset" aria-label="close"></button>
            </header>

            <section class="modal-card-body">
                <div class="field">
                    <label class="label">Название</label>
                    <div class="control">
                        <input name="title" class="input is-primary mb-4 is-large" type="text"
							   placeholder="Название группы" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Описание</label>
                    <div class="control">
                        <input name="description" class="input is-primary mb-4 " type="text" placeholder="Описание">
                    </div>
                </div>
                <label class="checkbox">
                    <input name="isPrivate" type="checkbox">
                    Публичная группа
                </label>
            </section>
            <footer class="modal-card-foot">
                <button class="button is-success" type="submit">Создать</button>
                <button class="button is-warning" type="reset">Сброс</button>
            </footer>
        </div>
    </div>
</form>

