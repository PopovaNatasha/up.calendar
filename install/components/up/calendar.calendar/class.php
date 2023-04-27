<?php

use Bitrix\Main\Context,
	Up\Calendar\Calendar,
	Bitrix\Main\Loader;

class CalendarCalendarComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        Loader::includeModule('up.calendar');
        $this->fetchTeams();
        $this->includeComponentTemplate();

		$request = Context::getCurrent()->getRequest();
		if ($request->isPost())
		{
			$post = $request->getPostList()->toArray();
			$this->changeColor($post);
			header("Refresh: 0");
		}
    }

    protected function fetchTeams(): void
	{
		global $USER;
		$teams = Calendar::getUserTeams($USER->getID());
		$idTeams = array_column($teams, 'ID_TEAM');

		$this->arResult['teams'] = $teams;
		$this->arResult['idTeams'] = $idTeams;
    }

	protected function changeColor(array $colorTeams): void
	{
		Calendar::setUserTeamColor($colorTeams);
	}
}