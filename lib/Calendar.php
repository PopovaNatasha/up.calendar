<?php

namespace Up\Calendar;

use Up\Calendar\Model\TeamTable;
use Up\Calendar\Model\UserTeamTable;

Class Calendar
{
    public static function getTeams($id = '', $query = '')
    {
        if (!$id) {
        if (!$query) {
            $nav = new \Bitrix\Main\UI\PageNavigation("page");
            $nav->allowAllRecords(false)
                ->setPageSize(5)
                ->initFromUri();

            $result = \Up\Calendar\Model\TeamTable::getList([    // // Тут Каталог групп с тегом паблик
                'select' => ['TITLE', 'ID_ADMIN', 'ID'],
                'filter' => ['IS_PRIVATE' => false],
                'count_total' => true,
                'offset' => $nav->getOffset(),
                'limit' => $nav->getLimit(),
            ],
            );
            $nav->setRecordCount($result->getCount());
        } else
        {
            $nav = new \Bitrix\Main\UI\PageNavigation("page");
            $nav->allowAllRecords(false)
                ->setPageSize(5)
                ->initFromUri();

            $result = \Up\Calendar\Model\TeamTable::getList([    // Тут Каталог групп с тегом паблик и поиском
                'select' => ['TITLE', 'ID_ADMIN', 'ID'],
                'filter' => [
                    'LOGIC' => 'AND',
                    '=%TITLE' => "%$query%",
                    ['IS_PRIVATE' => false]
                ],
                'count_total' => true,
                'offset' => $nav->getOffset(),
                'limit' => $nav->getLimit(),
            ],
            );
            $nav->setRecordCount($result->getCount());
        }
        }
        else {
            if (!$query)
            {
                $nav = new \Bitrix\Main\UI\PageNavigation("page");
                $nav->allowAllRecords(false)
                    ->setPageSize(5)
                    ->initFromUri();

                $result = \Up\Calendar\Model\TeamTable::getList([    // Тут выводятся группы пользователя
                    'select' => ['TITLE', 'ID_ADMIN', 'ID'],
                    'filter' => ['USER.ID_USER' => $id],
                    'count_total' => true,
                    'offset' => $nav->getOffset(),
                    'limit' => $nav->getLimit(),
                ],
                );
                $nav->setRecordCount($result->getCount());


            } else
            {
                $nav = new \Bitrix\Main\UI\PageNavigation("page");
                $nav->allowAllRecords(false)
                    ->setPageSize(5)
                    ->initFromUri();

                $result = \Up\Calendar\Model\TeamTable::getList([    // Тут выводятся группы пользователя с поиском
                    'select' => ['TITLE', 'ID_ADMIN', 'ID'],
                    'filter' => [
                        'LOGIC' => 'AND',
                        '=%TITLE' => "%$query%",
                        'USER.ID_USER' => $id
                    ],
                    'count_total' => true,
                    'offset' => $nav->getOffset(),
                    'limit' => $nav->getLimit(),
                ],
                );
                $nav->setRecordCount($result->getCount());
            }
        }
        return ['teams' => $result, 'nav' => $nav];
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

	public static function getTeamById($id)
	{
		return TeamTable::getRowById($id);
	}

	public static function getParticipantsTeam($idTeam)
	{
		return UserTeamTable::getList([
			'select' => ['ID_USER'],
			'filter' => [
				'ID_TEAM' => $idTeam
			],
		])->fetchAll();
	}

	public static function leaveTeam($idTeam)
	{
		global $USER;
		$row = UserTeamTable::getByPrimary(['ID_USER' => $USER->getID(), 'ID_TEAM' => $idTeam])->fetchObject();
		$row->delete();
	}

	public static function joinTheTeam($idTeam)
	{
		global $USER;
		UserTeamTable::createObject()
					 ->setIdUser($USER->getID())
					 ->setIdTeam($idTeam)
					 ->save();
	}

    public static function createInviteLink($idTeam)
    {
        $array = array(rand(100,999) => array('a' => rand(01,99)), rand(100,999) => array('a' => rand(01,99)));
        $crc32 = sprintf('%u', crc32(serialize($array)));
        $inviteStr = base_convert($crc32, 10, 36);
        $result = TeamTable::getById($idTeam)->fetchObject();
        $result->setInviteLink($inviteStr)->save();
        return $inviteStr;
    }

    public static function getTeamByLink($link)
    {
        $team = TeamTable::getList([
            'select' => ['ID'],
            'filter' => [
                'INVITE_LINK' => $link
            ]
        ]);
        if($team)
        {
            $team->fetchObject();
        }
        return $team;
    }
}
