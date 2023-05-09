<?php

use Bitrix\Main\Application,
	Bitrix\Main\Loader,
	Up\Calendar\API\Team;

class CalendarTeamsComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		Loader::includeModule('up.calendar');
		$this->fetchTeamList();
	}

	protected function fetchTeamList()
	{
		global $APPLICATION;
		$request = Application::getInstance()->getContext()->getRequest();
		if ($request->get('query'))
		{
			$query = $request->get('query');
			$result = Team::getTeams('', $query);
		}
		else
		{
			$result = Team::getTeams();
		}
		$teams = $result['teams'];
		$nav = $result['nav'];
		$this->arResult['Teams'] = $teams;
		$this->includeComponentTemplate();
		$APPLICATION->IncludeComponent(
			"bitrix:main.pagenavigation",
			"",
			[
				"NAV_OBJECT" => $nav,
				"SEF_MODE" => "N",
			],
			false
		);
	}
}