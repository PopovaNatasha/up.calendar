<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Engine\Controller;
use Up\Calendar\API\Team;

class InviteLink extends Controller
{
    public function createInviteLinkAction($idTeam): ?string
    {
        return Team::createInviteLink($idTeam);
    }
}