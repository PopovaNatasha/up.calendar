<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Engine\Controller;

class Calendar extends Controller
{
	public function getEventsListAction($idTeam): ?array
	{
		return \Up\Calendar\Calendar::getEventsList($idTeam);
	}

	public function changeEventAction($event)
	{
		return \Up\Calendar\Calendar::changeEvent($event);
	}
}