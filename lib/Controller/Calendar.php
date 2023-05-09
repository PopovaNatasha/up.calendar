<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Application,
	Bitrix\Main\Engine\Controller,
	Up\Calendar\API\Event,
	Up\Calendar\API\Team,
	Bitrix\Main\Context,
	Bitrix\Main\Localization\Loc,
	Up\Calendar\Services\Schedule;
use Up\Calendar\Services\FlashMessage;

Loc::loadMessages(__FILE__);

class Calendar extends Controller
{
    public function getEventsListAction($idTeam): ?array
    {
        return Event::getEventsList($idTeam);
    }

	public function checkUserPermission(int $idTeam): bool
	{
		if (!check_bitrix_sessid() || !Team::userIsTeamAdmin($idTeam))
		{
			FlashMessage::setError(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_ADMIN"));
			return false;
		}

		return true;
	}

	public function createEventAction(): void
	{
		$app = Application::getInstance();
		$idTeam = (int)$app->getCurrentRoute()->getParameterValue('id');

		if ($this->checkUserPermission($idTeam))
		{
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
		if ($this->isRequired($fields['title']) || $this->isRequired($fields['date']))
		{
			FlashMessage::setError(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_REQUIRED"));
			return false;
		}

		$eventPeriod = Schedule::splitDateIntoTwo($fields['date']);

		if ($eventPeriod === false)
		{
			FlashMessage::setError(Loc::getMessage('UP_CALENDAR_FORMAT_DATE'));
			return false;
		}

		return true;
	}

	public function isRequired(string $string): bool
	{
		return trim($string) === '';
	}

	public function updateTeamAction(): void
	{
		$app = Application::getInstance();
		$idTeam = (int)$app->getCurrentRoute()->getParameterValue('id');

		if (!$this->checkUserPermission($idTeam))
		{
			LocalRedirect('/group/' . $idTeam . '/');
		}

		$request = Context::getCurrent()->getRequest()->getPostList()->toArray();
		if ($this->isRequired($request['title']))
		{
			FlashMessage::setError(Loc::getMessage('UP_CALENDAR_VALIDATOR_UPDATE_IS_REQUIRED'));
			LocalRedirect('/group/' . $idTeam . '/');
		}

		$result = Team::updateTeam($idTeam, $request['title'], $request['description'], $request['isPrivate']);
		if (!$result->isSuccess())
		{
			FlashMessage::setArray($result->getErrorMessages());
		}

		LocalRedirect('/group/' . $idTeam . '/');
	}

	public function leaveTeamAction(): void
	{
		$app = Application::getInstance();
		$idTeam = (int)$app->getCurrentRoute()->getParameterValue('id');

		if (!check_bitrix_sessid())
		{
			FlashMessage::setError(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_ADMIN"));
			LocalRedirect('/group/' . $idTeam . '/');
		}

		$result = Team::leaveTeam($idTeam);
		if (!$result->isSuccess())
		{
			FlashMessage::setArray($result->getErrorMessages());
		}

		LocalRedirect('/group/' . $idTeam . '/');
	}

	public function joinTeamAction(): void
	{
		$app = Application::getInstance();
		$idTeam = (int)$app->getCurrentRoute()->getParameterValue('id');

		if (!check_bitrix_sessid())
		{
			FlashMessage::setError(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_ADMIN"));
			LocalRedirect('/group/' . $idTeam . '/');
		}

		$result = Team::joinTheTeam($idTeam);
		if (!$result->isSuccess())
		{
			FlashMessage::setArray($result->getErrorMessages());
		}

		LocalRedirect('/group/' . $idTeam . '/');
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