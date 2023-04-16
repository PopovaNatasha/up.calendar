<?php
/**
 * @var CMain $APPLICATION
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Group");
$APPLICATION->IncludeComponent('up:calendar.team-detail', '', []);

?>


<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>