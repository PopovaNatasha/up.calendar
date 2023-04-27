<?php

namespace Up\Calendar;

use Bitrix\Main\UI\PageNavigation,
    Up\Calendar\Model\TeamTable,
    Up\Calendar\Model\UserTeamTable,
    Up\Calendar\Model\EventTable,
    Up\Calendar\Model\RegularEventTable;
use Bitrix\Translate\Controller\Index\CollectLangPath;
use Up\Calendar\Model\UserStoryTable;

class Calendar
{
    public static function getTeams($id = '', $query = '')
    {
        if (!$id) {
            if (!$query) {
                $nav = new PageNavigation("page");
                $nav->allowAllRecords(false)
                    ->setPageSize(5)
                    ->initFromUri();

                $result = TeamTable::getList([    // // Тут Каталог групп с тегом паблик
                    'select' => ['TITLE', 'ID_ADMIN', 'ID'],
                    'filter' => ['IS_PRIVATE' => false],
                    'count_total' => true,
                    'offset' => $nav->getOffset(),
                    'limit' => $nav->getLimit(),
                ],
                );
                $nav->setRecordCount($result->getCount());
            } else {
                $nav = new PageNavigation("page");
                $nav->allowAllRecords(false)
                    ->setPageSize(5)
                    ->initFromUri();

                $result = TeamTable::getList([    // Тут Каталог групп с тегом паблик и поиском
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
        } else {
            if (!$query) {
                $nav = new PageNavigation("page");
                $nav->allowAllRecords(false)
                    ->setPageSize(5)
                    ->initFromUri();

                $result = TeamTable::getList([    // Тут выводятся группы пользователя
                    'select' => ['TITLE', 'ID_ADMIN', 'ID'],
                    'filter' => ['USER.ID_USER' => $id],
                    'count_total' => true,
                    'offset' => $nav->getOffset(),
                    'limit' => $nav->getLimit(),
                ],
                );
                $nav->setRecordCount($result->getCount());


            } else {
                $nav = new PageNavigation("page");
                $nav->allowAllRecords(false)
                    ->setPageSize(5)
                    ->initFromUri();

                $result = TeamTable::getList([    // Тут выводятся группы пользователя с поиском
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

    public static function createTeam($arguments): void
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

    public static function deleteTeam($id): void
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

    public static function getUserTeams($idUser)
    {
        return UserTeamTable::getList([
            'select' => ['ID_TEAM', 'COLOR', 'TITLE' => 'TEAM.TITLE'],
            'filter' => [
                'ID_USER' => $idUser
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
        $array = array(rand(100, 999) => array('a' => rand(01, 99)), rand(100, 999) => array('a' => rand(01, 99)));
        $crc32 = sprintf('%u', crc32(serialize($array)));
        $inviteStr = base_convert($crc32, 10, 36);
        $result = TeamTable::getById($idTeam)->fetchObject();
        $result->setInviteLink($inviteStr)->save();
        return $inviteStr;
    }

    public static function getTeamByLink($link)
    {

        $team = TeamTable::getList([
            'select' => ['ID', 'TITLE', 'DESCRIPTION'],
            'filter' => [
                'INVITE_LINK' => $link
            ],
        ])->fetch();
        return $team;
    }

    public static function updateTeam($idTeam, $arguments)
    {
        $team = TeamTable::getByPrimary(['ID' => (int)$idTeam])->fetchObject();
        if (!$team) {
            throw new \Exception('Group not found');
        }

        $idOldImage = $team->getPersonalPhoto();
        if ($_FILES['img']['name'] !== '') {
            $idImage = self::saveTeamImage();
            $team->setPersonalPhoto($idImage);
        }

        $teamTitle = trim($arguments['title']);
        if ($teamTitle === '') {
            throw new \Exception('Title can not be empty');
        }

        $team->setTitle($teamTitle)
            ->setDescription($arguments['description'] ?: '')
            ->setIsPrivate(!$arguments['isPrivate'])
            ->save();

        if ($_FILES['img']['name'] !== '') {
            \CFile::Delete($idOldImage);
        }
    }

    public static function saveTeamImage()
    {
        $arImage = $_FILES['img'];
        $arImage['MODULE_ID'] = 'up.calendar';
        $idImage = \CFile::SaveFile($arImage, 'up.calendar', false, false, 'team_image');

        if (!((int)$idImage > 0)) {
            throw new \Exception('Failed to save file');
        }

        return (int)$idImage;
    }

    public static function createEvent($arguments)
    {
        EventTable::createObject()
            ->setIdTeam($arguments['team_id'])
            ->setTitle($arguments['title'])
            ->setDateTimeFrom($arguments['date_from'])
            ->setDateTimeTo($arguments['date_to'])
            ->save();
    }

    public static function createRegularEvent($arguments)
    {
        RegularEventTable::createObject()
            ->setIdTeam($arguments['team_id'])
            ->setTitle($arguments['title'])
            ->setDateTimeFrom($arguments['date_from'])
            ->setDateTimeTo($arguments['date_to'])
            ->setDayStep($arguments['rule_repeat_count'])
            ->save();
    }

    public static function getEventsList($idTeam)
    {
        $singleEvents = EventTable::getList([
            'select' => ['*'],
            'filter' => [
                '@ID_TEAM' => $idTeam
            ],
        ])->fetchAll();

        $regularEvents = RegularEventTable::getList([
            'select' => ['*'],
            'filter' => [
                '@ID_TEAM' => $idTeam
            ],
        ])->fetchAll();

		global $USER;
		$id = $USER->getID();
		$userStoryEvents = UserStoryTable::getList([
			'select' => ['*'],
			'filter' => [
				'ID_USER' => $id
			]
		])->fetchAll();

        return ['events' => [
            'singleEvents' => $singleEvents,
            'regularEvents' => $regularEvents,
			'userStoryEvents' => $userStoryEvents,
        ]];
    }

	public static function setUserTeamColor(array $colorTeams): void
	{
		global $USER;
		$idUser = $USER->getId();
		$idTeam = $colorTeams['id'];
		$result = UserTeamTable::update(['ID_USER' => $idUser, 'ID_TEAM' => $idTeam], ['COLOR' => $colorTeams['color']]);

		if (!$result->isSuccess())
		{
			$errors = $result->getErrorMessages();
		}
	}
}