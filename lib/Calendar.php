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
                'select' => ['TITLE', 'ID_ADMIN'],
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
                'select' => ['TITLE', 'ID_ADMIN'],
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
                    'select' => ['TITLE', 'ID_ADMIN'],
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
                    'select' => ['TITLE', 'ID_ADMIN'],
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
}
