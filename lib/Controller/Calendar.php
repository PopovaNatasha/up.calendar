<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Application,
	Bitrix\Main\Engine\Controller,
	Up\Calendar\API\Event,
	Up\Calendar\API\Team,
	Bitrix\Main\Context,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Error,
	Bitrix\Main\SystemException;

class Calendar extends Controller
{
    public function getEventsListAction($idTeam): ?array
    {
        return Event::getEventsList($idTeam);
    }

	public function createEventAction()
	{
		$app = Application::getInstance();
		$idTeam = (int)$app->getCurrentRoute()->getParameterValue('id');

		if (!check_bitrix_sessid())
		{
			$this->addError(new Error(Loc::getMessage("UP_CALENDAR_VALIDATOR_CSRF")));
			return null;
		}

		if (!Team::userIsTeamAdmin($idTeam))
		{
			$this->addError(new Error(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_ADMIN")));
			return null;
		}

		$request = Context::getCurrent()->getRequest()->getPostList()->toArray();

		if (trim($request['title']) === '' || trim($request['date']) === '')
			{
				$this->addError(new Error(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_REQUIRED")));
				return null;
			}

		$eventPeriod = $this->formatDateTime($request['date']);

		if ($eventPeriod === false)
			{
				$this->addError(new Error(Loc::getMessage('UP_CALENDAR_FORMAT_DATE')));
				return null;
			}

		return Event::createEvent($idTeam, $request['title'], $eventPeriod['start'], $eventPeriod['end'], $request['rule_repeat'], (int)$request['rule_repeat_count']);
	}

	public function formatDateTime(string $date)
	{
		$eventDates = explode(' - ', $date);
		$eventPeriod = [];
		try
		{
			$eventPeriod['start'] = new DateTime($eventDates[0], "d.m.Y H:i");
			$eventPeriod['end'] = new DateTime($eventDates[1], "d.m.Y H:i");
		}
		catch (SystemException $e)
		{
			return false;
		}

		return $eventPeriod;
	}

    public function changeEventAction($event)
    {
        return Event::changeEvent($event);
    }

	public function deleteEventAction($event)
	{
		return Event::deleteEvent($event);
	}
}