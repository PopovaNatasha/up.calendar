<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Application,
	Bitrix\Main\Engine\Controller,
	Up\Calendar\API\Event,
	Up\Calendar\API\Team,
	Bitrix\Main\Context,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Error,
	Up\Calendar\Services\Schedule;
use Up\Calendar\Services\FlashMessage;

Loc::loadMessages(__FILE__);

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

		if (!check_bitrix_sessid() || !Team::userIsTeamAdmin($idTeam))
		{
			FlashMessage::set(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_ADMIN"));
			LocalRedirect('/group/' . $idTeam . '/');
		}

		$request = Context::getCurrent()->getRequest()->getPostList()->toArray();
		if (!$this->validateFields($request))
		{
			LocalRedirect('/group/' . $idTeam . '/');
		}

		$eventPeriod = Schedule::splitDateIntoTwo($request['date']);

		$result = Event::createEvent($idTeam, $request['title'], $eventPeriod['start'], $eventPeriod['end'], $request['rule_repeat'], (int)$request['rule_repeat_count']);
		if (!$result->isSuccess())
		{
			FlashMessage::setArray($result->getErrorMessages());
		}

		LocalRedirect('/group/' . $idTeam . '/');
	}

	public function validateFields(array $fields): bool
	{
		if (trim($fields['title']) === '' || trim($fields['date']) === '')
		{
			FlashMessage::set(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_REQUIRED"));
			return false;
		}

		$eventPeriod = Schedule::splitDateIntoTwo($fields['date']);

		if ($eventPeriod === false)
		{
			FlashMessage::set(Loc::getMessage('UP_CALENDAR_FORMAT_DATE'));
			return false;
		}

		return true;
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