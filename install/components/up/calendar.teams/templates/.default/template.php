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
        <a class="group-card block"
           href="/group/<?= $team['ID'] ?>/">
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


