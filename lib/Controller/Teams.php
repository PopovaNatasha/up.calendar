<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Application,
	Bitrix\Main\Engine\Controller,
	Up\Calendar\API\Team,
	Bitrix\Main\Context,
	Bitrix\Main\Localization\Loc,
	Up\Calendar\Services\FlashMessage;

Loc::loadMessages(__FILE__);

class Teams extends Controller
{
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

	public function checkUserPermission(int $idTeam): bool
	{
		if (!check_bitrix_sessid() || !Team::userIsTeamAdmin($idTeam))
		{
			FlashMessage::setError(Loc::getMessage("UP_CALENDAR_VALIDATOR_IS_ADMIN"));
			return false;
		}

		return true;
	}

	public function isRequired(string $string): bool
	{
		return trim($string) === '';
	}
}