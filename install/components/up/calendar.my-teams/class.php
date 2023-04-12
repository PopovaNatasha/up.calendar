<?php

use Up\Calendar\Calendar;

class CalendarMyTeamsComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        \Bitrix\Main\Loader::includeModule('up.calendar');
        $this->fetchTeamList();
        $this->includeComponentTemplate();
    }

    protected function fetchTeamList()
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if ($request->getRequestMethod() === 'POST') {
            Calendar::createTeam($request->getPostList());
            LocalRedirect('/MyGroups');
            exit;
        } elseif ($request->getRequestMethod() === 'GET') {
            if ($request->get('query')) {
                $query = $request->get('query');
                $teams = Calendar::getTeams($query);
                $this->arResult['Teams'] = $teams;
            } else {
                $teams = Calendar::getTeams();
                $this->arResult['Teams'] = $teams;
            }
        }
    }
}