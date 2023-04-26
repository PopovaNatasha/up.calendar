<?php

namespace Up\Calendar\Controller;

use Bitrix\Main\Engine\Controller;

class InviteLink extends Controller
{
    public function createInviteLinkAction($idTeam) : ?string
    {
        return \Up\Calendar\Calendar::createInviteLink($idTeam);
    }
}