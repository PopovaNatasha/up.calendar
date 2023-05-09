<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Context,
	Bitrix\Main\Engine\Controller,
	Bitrix\Main\Localization\Loc,
	Up\Calendar\API\Team,
	Up\Calendar\Services\FlashMessage;

Loc::loadMessages(__FILE__);

class User extends Controller
{
	public function changeColorAction(): void
	{
		$request = Context::getCurrent()->getRequest()->getPostList()->toArray();
		$idTeam = (int)$request['id'];

		$result = Team::setUserTeamColor($idTeam, $request['color']);
		if (!$result->isSuccess())
		{
			FlashMessage::setArray($result->getErrorMessages());
		}

		LocalRedirect('/');
	}
}