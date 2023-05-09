<?php

use Bitrix\Main\Context,
	Up\Calendar\API\Team,
    Up\Calendar\API\Event,
	Bitrix\Main\Loader;

class CalendarCalendarComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        Loader::includeModule('up.calendar');
        $this->fetchTeams();
        $this->includeComponentTemplate();
    }

    protected function fetchTeams(): void
	{
		global $USER;
		$teams = Team::getUserTeams($USER->getID());
		$idTeams = array_column($teams, 'ID_TEAM');

		$this->arResult['teams'] = $teams;
		$this->arResult['idTeams'] = $idTeams;
    }
}