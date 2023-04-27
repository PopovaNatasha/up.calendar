<?php

use Bitrix\Main\Loader,
    Up\Calendar\Calendar,
    Bitrix\Main\Context;

class CalendarInviteComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        Loader::includeModule('up.calendar');
        $request = Context::getCurrent()->getRequest();
        if ($request->isPost())
        {
            $this->actionPost($request->getPostList());
        }
            $this->actionGet();
        $this->includeComponentTemplate();
    }

    protected function actionGet()
    {
        global $USER;
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $team = Calendar::getTeamByLink($request->get('link'));
        $team['link'] = $request->get('link');
        $participants = Calendar::getParticipantsTeam($team['ID']);

        $check = 0;
        foreach ($participants as $participant)
        {
            if ($participant['ID_USER'] === $USER->getID())
            {
                $check = 1;
            }
        }
        if ($check === 1)
        {
            LocalRedirect('/group/' . $team['ID']. '/');
        }

        $this->arResult = $team;
    }

    protected function actionPost($post)
    {

        $team = Calendar::getTeamByLink($post['link']);
        Calendar::joinTheTeam($team['ID']);
        LocalRedirect("/group/{$team['ID']}/");
    }

}