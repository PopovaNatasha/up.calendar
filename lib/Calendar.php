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
                    ->setIsPrivate($arguments['isPrivate'] ?: 0)
                    ->save();


           $title = $arguments['title'];
           $idAdmin = $arguments['adminId'];
           $team = TeamTable::getList([
               'filter' => [
                   'LOGIC' => 'AND',
                   '=TITLE' => $title,
                   '=ID_ADMIN' => $idAdmin,
               ]
           ]);
           $team->fetch();
           $idTeam = $team->{'ID'};
            var_dump($idTeam); die;
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
