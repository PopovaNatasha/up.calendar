<?php
global $USER;
/**
 * @var CMain $APPLICATION
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/up.calendar/install/templates/calendar/components/bitrix/main.profile/template.php");
$APPLICATION->SetTitle("HobbyPlan");
$APPLICATION->IncludeComponent('up:calendar.calendar', '', []);
?>


<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>