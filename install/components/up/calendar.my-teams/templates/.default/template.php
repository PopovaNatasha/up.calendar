<?php
/**
 * @var object $USER
 * @var array $arResult
 */

?>
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
        <a class="group-card block <?= ($team['ID_ADMIN'] === $USER->getID()) ? 'admined' : ''; ?>"
           href="/group/<?= $team['ID'] ?>/">
            <div class="block group">
                <div>
                    <figure class="image is-64x64">
                        <?php if ($team['PERSONAL_PHOTO']): ?>
                            <?= \CFile::ShowImage($team['PERSONAL_PHOTO'], 64, 64) ?>
                        <?php else: ?>
                            <img src="https://bulma.io/images/placeholders/64x64.png" alt="Placeholder image">
                        <?php endif; ?>
                    </figure>

                </div>
                <p style="margin-left: 15px; font-size: large"><?= htmlspecialchars($team['TITLE']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<form name="Create Team" action="" method="post">
    <input name="adminId" type="hidden" value="<?= $USER->getID() ?>">
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
                               placeholder="Название группы">
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
                <button class="button" type="reset">Отмена</button>
            </footer>
        </div>
    </div>
</form>

