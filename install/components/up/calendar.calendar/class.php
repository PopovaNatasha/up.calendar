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
		$result = Calendar::getTeams($USER->getID());

		$teams = [];
		$idTeams = [];
		foreach ($result['teams'] as $team)
		{
			$idTeams[] = $team['ID'];
			$teams[] = [
				'id' => $team['ID'],
				'title' => $team['TITLE'],
				'idAdmin' => $team['ID_ADMIN'],
			];
		}
		$this->arResult['idTeams'] = $idTeams;
		$this->arResult['teams'] = $teams;

    }
}