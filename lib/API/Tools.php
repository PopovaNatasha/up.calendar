<?php

namespace Up\Calendar\API;

use Up\Calendar\Model\TeamTable;

function userIsTeamAdmin($idTeam) :bool
{
    global $USER;
    $idTeam = (int)$idTeam;
    $team = TeamTable::getList([
        'select' => ['ID_ADMIN'],
        'filter' => [
            'ID' => $idTeam
        ]
    ])->fetchObject();
    if ((int)$USER->getId() === (int)$team['ID_ADMIN']) {
        return true;
    }
    return false;
}