<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Engine\Controller,
	Up\Calendar\API\Event;

class Calendar extends Controller
{
    public function getEventsListAction($idTeam): ?array
    {
        return Event::getEventsList($idTeam);
    }

    public function changeEventAction($event): bool
    {
        return Event::changeEvent($event);
    }

	public function deleteEventAction($event): bool
	{
		return Event::deleteEvent($event);
	}
}