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
			createEvent($post);
		}
		$this->includeComponentTemplate();
	}

	protected function createEvent($arguments)
	{
		if ($arguments['title'] === '')
		{
			throw new Exception('Title can not be empty!');
		}
	}
}