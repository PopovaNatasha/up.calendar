<?php

class CalendarCalendarComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		\Bitrix\Main\Loader::includeModule('up.calendar');
		$this->includeComponentTemplate();
	}

}