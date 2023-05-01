<?php
/**
 * @var object $USER
 * @var array $arResult
 */
?>
<div class="columns is-mobile">
    <div class="column is-8">
        <form action="/groups/?" method="get">
            <input class="input is-primary" type="text" placeholder="Поиск по группам" name="query"
                   value="<?= htmlspecialchars($_GET['query']) ?>">
        </form>
    </div>
</div>

<div class="block">
    <?php foreach ($arResult['Teams'] as $team): ?>
        <a class="group-card block <?= ($team['ID_ADMIN'] == $USER->getID()) ? 'admined' : ''; ?>"
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


