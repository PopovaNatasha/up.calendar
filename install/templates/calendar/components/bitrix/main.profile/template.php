<?php
/**
 * @var object $USER
 */
?>
<style>
    .user-info {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .FirstName {
        padding: 10px;
    }
</style>
<div class="columns">
    <div class="column is-two-fifths">
        <div class="user-info is-left">
            <figure class="image is-64x64">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/ico.jpg">
            </figure>
            <div class="FirstName"><?= $USER->GetFullName() ?></div>
        </div>
    </div>
</div>

<div class="columns">
    <div class="column is-link is-2">
        <a class="box" href="/" style="display:block; height: 100%;">
            <figure class="image is-32x32">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/calendar.png">
            </figure>
            Расписание
        </a>
    </div>
    <div class="column is-link is-2">
        <a class="box" href="/MyGroups" style="display:block; height: 100%;">
            <figure class="image is-32x32">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/groups.png">
            </figure>
            Мои группы
        </a>
    </div>
    <div class="column is-link is-2">
        <a class="box" href="/AllGroups" style="display:block; height: 100%;">
            <figure class="image is-32x32 ">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/groups.png">
            </figure>
            Каталог групп
        </a>
    </div>
    <div class="column is-link is-2">
        <a class="box" href="/Profile" style="display:block; height: 100%;">
            <figure class="image is-32x32">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/profile.jpg">
            </figure>
            Мой профиль
        </a>
    </div>
</div>