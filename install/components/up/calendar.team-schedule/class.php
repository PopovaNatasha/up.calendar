<?php

use Bitrix\Main\Loader,
	Up\Calendar\Calendar,
	Bitrix\Main\Context;

class CalendarScheduleComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		Loader::includeModule('up.calendar');
		$request = Context::getCurrent()->getRequest();
		if ($request->isPost())
		{
			$post = $request->getPostList()->toArray();
			$this->createEvent($post, $request->get('id'));
		}
		$this->getTeam($request->get('id'));
		$this->includeComponentTemplate();
	}

	protected function getTeam($teamId): void
	{
		$teamId = (int)$teamId;
		$team = Calendar::getTeamById($teamId);
		$this->arResult['teamId'] = [$teamId];
		$this->arResult['team'] = $team;
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
				Calendar::createEvent($arguments);
				break;
			case 'daily':
				Calendar::createRegularEvent($arguments);
				break;
			case 'weekly':
				$arguments['rule_repeat_count'] = 7;
				Calendar::createRegularEvent($arguments);
				break;
			default:
				throw new Exception('Invalid type repeat rule');
		}
	}
}