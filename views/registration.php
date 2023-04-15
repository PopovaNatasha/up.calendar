<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("registration");
?><?$APPLICATION->IncludeComponent("bitrix:main.register", "template", Array(
	"AUTH" => "Y",	// Автоматически авторизовать пользователей
		"REQUIRED_FIELDS" => array(	// Поля, обязательные для заполнения
			0 => "EMAIL",
			2 => "NAME",
			4 => "LAST_NAME",
		),
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"SHOW_FIELDS" => array(	// Поля, которые показывать в форме
			0 => "EMAIL",
			2 => "NAME",
			4 => "LAST_NAME",
		),
		"SUCCESS_PAGE" => "",	// Страница окончания регистрации
		"USER_PROPERTY" => "",	// Показывать доп. свойства
		"USER_PROPERTY_NAME" => "",	// Название блока пользовательских свойств
		"USE_BACKURL" => "Y",	// Отправлять пользователя по обратной ссылке, если она есть
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>