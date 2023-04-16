<?php
use Bitrix\Main\Loader,
    Up\Calendar\Calendar;

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
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if ($request->get('query'))
            {
                $query = $request->get('query');
                $result = Calendar::getTeams('',$query);
                $teams = $result['teams'];
                $nav = $result['nav'];
                $this->arResult['Teams'] = $teams;
            } else
            {
                $result = Calendar::getTeams();
                $teams = $result['teams'];
                $nav = $result['nav'];
                $this->arResult['Teams'] = $teams;
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