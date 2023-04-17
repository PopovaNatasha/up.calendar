<?php
/**
 * @var $arResult
 */
?>
<div class="box">
    <div class="title is-centered"><?=$arResult['TITLE']?></div>
    <div class="title is-centered"><?=$arResult['DESCRIPTION']?></div>
        <form class="buttons" method="post">
            <input type="hidden" name="link" value="<?= $arResult['link'] ?>"/>
            <input type="hidden" name="action" value="in"/>
            <button class="button is-primary is-light" style="margin-left: auto">Вcтупить</button>
        </form>
</div>