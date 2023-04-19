<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Engine\Controller;

class Calendar extends Controller
{
	public function getEventsListAction(int $idTeam): ?array
	{
		return \Up\Calendar\Calendar::getEventsForTeam($idTeam);
	}
}