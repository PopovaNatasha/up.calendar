<?php
/**
 * @var CMain $APPLICATION
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (!$USER->IsAuthorized())
{
	$APPLICATION->SetTitle("auth");
	$APPLICATION->IncludeComponent("bitrix:system.auth.form", "template", Array(
	"FORGOT_PASSWORD_URL" => "",	// Страница забытого пароля
	"PROFILE_URL" => "",	// Страница профиля
	"REGISTER_URL" => "",	// Страница регистрации
	"SHOW_ERRORS" => "N",	// Показывать ошибки
),
									   false
);
}
else
{
	require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/up.calendar/install/templates/calendar/components/bitrix/main.profile/template.php");
	$APPLICATION->SetTitle("HobbyPlan");
	$APPLICATION->IncludeComponent('up:calendar.calendar', '', []);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");