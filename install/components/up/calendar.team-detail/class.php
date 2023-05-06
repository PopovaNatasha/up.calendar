<?php

use Bitrix\Main\Application,
	Bitrix\Main\Loader,
	Up\Calendar\API\Team,
    Up\Calendar\API\Event,
	Bitrix\Main\Context;

class CalendarCalendarComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		Loader::includeModule('up.calendar');
        $this->getTeam();
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
                LocalRedirect('');
			}
            elseif ($post['rule_repeat'])
            {
                $this->createEvent($post, $request->get('id'));
            }
		}
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

	protected function actionTeam($idTeam): void
	{
		$request = Context::getCurrent()->getRequest()->getPostList()->toArray();
		if ($request['action'] === 'in')
		{
            Team::joinTheTeam($idTeam);
		}
		elseif ($request['action'] === 'out')
		{
            Team::leaveTeam($idTeam);
		}
	}

	protected function updateTeam($idTeam, $post)
	{
        Team::updateTeam($idTeam, $post);
	}

    protected function createEvent($arguments, $teamId): void
    {
        if ($arguments['title'] === '' || $arguments['date'] === '')
        {
            throw new Exception('Not all required fields are filled');
        }

        $arguments['team_id'] = $teamId;
        $eventDate = explode(' - ', $arguments['date']);
        $arguments['date_from'] = new \Bitrix\Main\Type\DateTime($eventDate[0], "d.m.Y H:i");
        $arguments['date_to'] = new \Bitrix\Main\Type\DateTime($eventDate[1], "d.m.Y H:i");
        unset($arguments['date']);

        switch ($arguments['rule_repeat'])
        {
            case 'non':
                Event::createEvent($arguments);
                break;
            case 'daily':
                Event::createRegularEvent($arguments);
                break;
            case 'weekly':
                $arguments['rule_repeat_count'] = 7;
                Event::createRegularEvent($arguments);
                break;
            default:
                throw new Exception('Invalid type repeat rule');
        }
    }
}