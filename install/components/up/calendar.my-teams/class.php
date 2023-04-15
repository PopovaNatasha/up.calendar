<?php
use Bitrix\Main\Loader,
	Up\Calendar\Calendar;

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
            Calendar::createTeam($request->getPostList());
            LocalRedirect('/groups/my/');
            exit;
        } elseif ($request->getRequestMethod() === 'GET') {
            if ($request->get('query')) {
                $query = $request->get('query');
                $result = Calendar::getTeams($USER->getID(),$query);
                $teams = $result['teams'];
                $nav = $result['nav'];
                $this->arResult['Teams'] = $teams;
            } else {
                $result = Calendar::getTeams($USER->getID());
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