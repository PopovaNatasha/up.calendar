<?php

use Up\Calendar\Calendar;

class CalendarCalendarComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        \Bitrix\Main\Loader::includeModule('up.calendar');
        $this->fetchTeams();
        $this->includeComponentTemplate();
    }

    protected function fetchTeams()
    {
		global $USER;
		$result = Calendar::getUserTeams($USER->getID());

		$idTeams = [];
		foreach ($result as $team)
		{
			$idTeams[] = $team['ID_TEAM'];
		}
		$this->arResult = $idTeams;
    }
}