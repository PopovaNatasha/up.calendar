<?php
global $USER;
/**
 * @var CMain $APPLICATION
 */

// define('NEED_AUTH', true);

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");


$APPLICATION->SetTitle("Hobby Calendar");
$APPLICATION->IncludeComponent('up:calendar.calendar', '', []);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");