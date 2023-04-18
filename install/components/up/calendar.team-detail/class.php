<?php

use Bitrix\Main\Loader,
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
		$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
		if ($request->get('id'))
		{
			$idTeam = $request->get('id');
            if(!is_numeric($idTeam))
            {
                $idTeam = (int)Calendar::getTeamByLink($idTeam);
            }
            else
            {
                $idTeam = (int)$idTeam;
            }
			$team = Calendar::getTeamById($idTeam);
            $team['link'] = Calendar::createInviteLink($idTeam);
			$participants = Calendar::getParticipantsTeam($idTeam);
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