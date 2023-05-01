<?php
use Bitrix\Main\Loader,
	Up\Calendar\API\Team;

class CalendarMyTeamsComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        Loader::includeModule('up.calendar');
        $this->fetchTeamList();

    }

    protected function fetchTeamList()
    {
        global $USER;
        global $APPLICATION;
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if ($request->getRequestMethod() === 'POST') {
            Team::createTeam($request->getPostList());
            LocalRedirect('/groups/my/');
            exit;
        } elseif ($request->getRequestMethod() === 'GET') {
            if ($request->get('query')) {
                $query = $request->get('query');
                $result = Team::getTeams($USER->getID(),$query);
                $teams = $result['teams'];
                $nav = $result['nav'];
                $this->arResult['Teams'] = $teams;
            } else {
                $result = Team::getTeams($USER->getID());
                $teams = $result['teams'];
                $nav = $result['nav'];
                $this->arResult['Teams'] = $teams;
            }
        }
        $this->includeComponentTemplate();
        $APPLICATION->IncludeComponent(
            "bitrix:main.pagenavigation",
            "",
            array(
                "NAV_OBJECT" => $nav,
                "SEF_MODE" => "N",
            ),
            false
        );
    }
}