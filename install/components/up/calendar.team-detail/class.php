<?php

use Bitrix\Main\Application,
	Bitrix\Main\Loader,
	Up\Calendar\Calendar,
	Bitrix\Main\Context;

class CalendarCalendarComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		\Bitrix\Main\Loader::includeModule('up.calendar');
		$this->getTeam();
		$this->includeComponentTemplate();

		$request = Context::getCurrent()->getRequest();
		if ($request->isPost())
		{
			$post = $request->getPostList()->toArray();
			if ($post['action'])
			{
				$this->actionTeam((int)$request->get('id'));
			}
			elseif ($post['settings'])
			{
				$this->updateTeam($request->get('id'), $post);
			}
			header("Refresh: 0");
		}

	}

	protected function getTeam()
	{
        global $USER;
        $request = Application::getInstance()->getContext()->getRequest();
		if ($request->get('id'))
		{
			$idTeam = (int)$request->get('id');
			$team = Calendar::getTeamById($idTeam);
            if (!$team)
            {
                LocalRedirect('/404');
            }
            $participants = Calendar::getParticipantsTeam($idTeam);

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
                $team['INVITE_LINK'] = Calendar::createInviteLink($idTeam);
            }

			$this->arResult = $team;
			$this->arResult['PARTICIPANTS'] = $participants;
		}
	}

	protected function actionTeam($idTeam): void
	{
		$request = Context::getCurrent()->getRequest()->getPostList()->toArray();
		if ($request['action'] === 'in')
		{
			Calendar::joinTheTeam($idTeam);
		}
		elseif ($request['action'] === 'out')
		{
			Calendar::leaveTeam($idTeam);
		}
	}

	protected function updateTeam($idTeam, $post)
	{
		Calendar::updateTeam($idTeam, $post);
	}
}