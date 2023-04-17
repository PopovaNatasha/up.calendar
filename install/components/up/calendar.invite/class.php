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
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $team = Calendar::getTeamByLink($request->get('link'));
        $team['link'] = $request->get('link');
        $this->arResult = $team;
    }

    protected function actionPost($post)
    {
        $team = Calendar::getTeamByLink($post['link']);
        // @Togo проверка юзер уже в группе или нет
        Calendar::joinTheTeam($team['ID']);
        LocalRedirect("/group/{$team['ID']}/");
    }

}