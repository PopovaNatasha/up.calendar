<?php

namespace Up\Calendar;

use Up\Calendar\Model\TeamTable;
use Up\Calendar\Model\UserTeamTable;

Class Calendar
{
    public static function getTeams($query = '')
    {
        if (!$query)
        {
            $result = TeamTable::getList(['select' => ['*']]);
        } else
        {
            $result = TeamTable::getList([
                'filter' => [
                    'LOGIC' => 'OR',
                    '=%TITLE' => "%$query%",
                    '=%DESCRIPTION' => "%$query%",
                ]
            ]);
        }
        return $result->fetchAll();
    }

    public static function createTeam($arguments) : void
    {
       $result = TeamTable::createObject()
                    ->setTitle($arguments['title'])
                   ->setDescription($arguments['description'] ?: '')
                    ->setIdAdmin($arguments['adminId'])
                    ->setIsPrivate(!$arguments['isPrivate'])
                    ->save();

	   $idTeam = $result->getId();
	   UserTeamTable::createObject()
					->setIdUser($arguments['adminId'])
					->setIdTeam($idTeam)
					->save();

    }

    public static function deleteTeam($id) : void
    {
        TeamTable::delete($id);
    }
}
