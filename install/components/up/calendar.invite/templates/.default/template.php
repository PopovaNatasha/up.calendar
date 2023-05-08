<?php
/**
 * @var $arResult
 */
?>
<div class="columns is-centered">
    <div class="column is-5">
        <div class="box">
            <div class="title is-centered"><?= htmlspecialchars($arResult['TITLE']) ?></div>
            <div class="title is-5 is-centered"><?= htmlspecialchars($arResult['DESCRIPTION']) ?></div>
            <form class="buttons" method="post">
                <input type="hidden" name="link" value="<?= $arResult['link'] ?>"/>
                <input type="hidden" name="action" value="in"/>
                <button class="button is-primary is-light" style="margin-left: auto">Вcтупить</button>
            </form>
        </div>
    </div>
</div>
