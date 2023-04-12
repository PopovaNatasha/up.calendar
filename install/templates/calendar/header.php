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
	<script src="https://kit.fontawesome.com/7f025501b5.js" crossorigin="anonymous"></script>
</head>
<body>
<?php $APPLICATION->ShowPanel(); ?>

<section class="section">
    <div class="container">
        <nav class="navbar has-shadow" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item has-text-weight-semibold is-size-4 logo" href="/">
                    <img class="" src="https://em-content.zobj.net/source/skype/289/calendar_1f4c5.png" >
                     <span>   </span> Hobby Calendar plan
                </a>

                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false"
                   data-target="navbarBasicExample">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>

            <div id="navbarBasicExample" class="navbar-menu">
                <div class="navbar-start">

                </div>

                <div class="navbar-end">

                </div>
            </div>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
