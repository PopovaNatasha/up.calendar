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

    .section.content {
        padding-top: 0px;
        background-color: #fafafa;
        margin-bottom: 0;
    }
</style>

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
        <a class="box" href="/groups/my/" style="display:block; height: 100%;">
            <figure class="image is-32x32">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/groups.png">
            </figure>
            Мои группы
        </a>
    </div>
    <div class="column is-link is-2">
        <a class="box" href="/groups/" style="display:block; height: 100%;">
            <figure class="image is-32x32 ">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/groups.png">
            </figure>
            Каталог групп
        </a>
    </div>
    <div class="column is-link is-2">
        <a class="box" href="/profile/" style="display:block; height: 100%;">
            <figure class="image is-32x32">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/profile.jpg">
            </figure>
            Мой профиль
        </a>
    </div>
</div>