<?php

use Bitrix\Main\Application,
	Bitrix\Main\Loader,
	Up\Calendar\API\Team,
	Bitrix\Main\Context;

class CalendarCalendarComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		Loader::includeModule('up.calendar');
        $this->getTeam();
        $this->includeComponentTemplate();
	}

	protected function getTeam()
	{
        global $USER;
        $request = Application::getInstance()->getContext()->getRequest();
		if ($request->get('id'))
		{
			$idTeam = (int)$request->get('id');
			$team = Team::getTeamById($idTeam);
            if (!$team)
            {
                LocalRedirect('/404');
            }
            $participants = Team::getParticipantsTeam($idTeam);

            if ($team['IS_PRIVATE'] === '1')
            {
                $check = 0;
                foreach ($participants as $participant)
                {
                    if ($participant['ID_USER'] === $USER->getID())
                    {
                        $check = 1;
                    }
                }
                if ($check === 0)
                {
                    LocalRedirect('/404');
                }
            }

            if(!$team['INVITE_LINK'])
            {
                $team['INVITE_LINK'] = Team::createInviteLink($idTeam);
            }

			$this->arResult = $team;
			$this->arResult['PARTICIPANTS'] = $participants;
		}
	}
}