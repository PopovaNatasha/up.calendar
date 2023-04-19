<?php

use Bitrix\Main\Loader,
	Up\Calendar\Calendar,
	Bitrix\Main\Context;

class CalendarScheduleComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		\Bitrix\Main\Loader::includeModule('up.calendar');
		$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		if ($request->isPost())
		{
			$post = $request->getPostList()->toArray();
			$this->createEvent($post, $request->get('id'));
		}
		$this->includeComponentTemplate();
	}

	protected function createEvent($arguments, $teamId)
	{
		if ($arguments['title'] === '' || $arguments['date'] === '')
		{
			throw new Exception('Not all required fields are filled');
		}

		$arguments['team_id'] = $teamId;
		$eventDate = explode(' - ', $arguments['date']);
		$arguments['date_from'] = new \Bitrix\Main\Type\DateTime($eventDate[0],"m/d/Y H:i");
		$arguments['date_to'] = new \Bitrix\Main\Type\DateTime($eventDate[1],"m/d/Y H:i");
		unset($arguments['date']);

		var_dump($arguments);
		// $dateFrom = strtotime($eventDate[0]);
		// $dateTo = strtotime($eventDate[1]);



	}
}