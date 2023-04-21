<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Engine\Controller;

class Calendar extends Controller
{
	public function getEventsListAction($idTeam): ?array
	{
		return \Up\Calendar\Calendar::getEventsList($idTeam);
	}
}