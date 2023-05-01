<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Engine\Controller;
use Up\Calendar\API\Event;

class Calendar extends Controller
{
    public function getEventsListAction($idTeam): ?array
    {
        return Event::getEventsList($idTeam);
    }

    public function changeEventAction($event)
    {
        return Event::changeEvent($event);
    }
}