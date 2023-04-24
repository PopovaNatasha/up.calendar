<?php
/**
 * @var CMain $APPLICATION
 */
?><!doctype html>
<html lang="<?= LANGUAGE_ID; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php $APPLICATION->ShowTitle(); ?></title>
    <?php $APPLICATION->ShowHead(); ?>

	<link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
	<script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
	<link href="https://unpkg.com/bulma-calendar@6.0.7/dist/css/bulma-calendar.min.css" rel="stylesheet">
	<script src="https://unpkg.com/bulma-calendar@6.0.7/dist/js/bulma-calendar.min.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
<!--	<script type = "text/JavaScript" src = " https://MomentJS.com/downloads/moment.js"></script>-->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment-with-locales.min.js"></script>

	<script src="https://kit.fontawesome.com/7f025501b5.js" crossorigin="anonymous"></script>
</head>
<body>
<?php $APPLICATION->ShowPanel(); ?>

<section class="section">
    <div class="container">
        <nav class="navbar has-shadow">
                <a class="navbar-item has-text-weight-semibold is-size-4 logo" href="/">
                    <img class="" src="https://em-content.zobj.net/source/skype/289/calendar_1f4c5.png" >
                     <span>   </span> Hobby Calendar plan
                </a>

                <div class="user-info">


                    <div class="FirstName"><?= $USER->GetFullName() ?></div>
                    <a class="button is-danger" href="/?logout=yes&<?=bitrix_sessid_get()?>">Выйти</a>
                </div>
        </nav>
    </div>
</section>

<section class="section content">
    <div class="container">
