<?php

namespace Up\Calendar\API;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc,
	Up\Calendar\Model\TeamTable,
    Up\Calendar\Model\UserTeamTable,
    Bitrix\Main\UI\PageNavigation,
	Bitrix\Main\Result;

class Team
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
                    'select' => ['TITLE', 'ID_ADMIN', 'ID', 'PERSONAL_PHOTO'],
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
                    'select' => ['TITLE', 'ID_ADMIN', 'ID', 'PERSONAL_PHOTO'],
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
                    'select' => ['TITLE', 'ID_ADMIN', 'ID', 'PERSONAL_PHOTO'],
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
                    'select' => ['TITLE', 'ID_ADMIN', 'ID', 'PERSONAL_PHOTO'],
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

        return ['teams' => $result->fetchAll(), 'nav' => $nav];
    }

    public static function createTeam($arguments): void
    {
        global $USER;
        $adminId = (int)$USER->getId();
        if(!$arguments['title'])
        {
            LocalRedirect('');
        }

        $result = TeamTable::createObject()
            ->setTitle($arguments['title'])
            ->setDescription($arguments['description'] ?: '')
            ->setIdAdmin($adminId)
            ->setIsPrivate(!$arguments['isPrivate'])
            ->save();

        $idTeam = $result->getId();
        UserTeamTable::createObject()
            ->setIdUser($adminId)
            ->setIdTeam($idTeam)
            ->save();

    }

    public static function getTeamById($id)
    {
        $result = TeamTable::getRowById($id);
        if (!$result) {
            $result = null;
        }
        return $result;
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

    public static function leaveTeam(int $idTeam): Result
    {
        global $USER;
        $row = UserTeamTable::getByPrimary(['ID_USER' => $USER->getID(), 'ID_TEAM' => $idTeam])->fetchObject();
		if (!$row)
		{
			return (new Result())->addError(new Error(Loc::getMessage('UP_CALENDAR_LEAVE_TEAM_ERROR')));
		}

		return $row->delete();
    }

    public static function joinTheTeam(int $idTeam): Result
    {
        global $USER;
        return UserTeamTable::createObject()
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

    public static function updateTeam(int $idTeam, string $title, string $description, string $isPrivate = null): Result
	{
        $team = TeamTable::getByPrimary(['ID' => $idTeam])->fetchObject();
        if (!$team)
		{
			return (new Result())->addError(new Error(Loc::getMessage('UP_CALENDAR_INVALID_ID_TEAM')));
        }

        $idOldImage = $team->getPersonalPhoto();
        if ($_FILES['img']['name'] !== '')
		{
            $idImage = self::saveTeamImage();
			if(!$idImage)
			{
				return (new Result())->addError(new Error(Loc::getMessage('UP_CALENDAR_FAILED_SAVE_IMG')));
			}
            $team->setPersonalPhoto($idImage);
        }

        $result = $team->setTitle($title)
					   ->setDescription($description ?: '')
					   ->setIsPrivate(!$isPrivate)
					   ->save();

        if ($_FILES['img']['name'] !== '')
		{
            \CFile::Delete($idOldImage);
        }

		return $result;
    }

    public static function saveTeamImage(): ?int
    {
        $arImage = $_FILES['img'];
        $arImage['MODULE_ID'] = 'up.calendar';

        if (!\CFile::IsImage($arImage['name']))
        {
            return null;
        }
        $idImage = \CFile::SaveFile($arImage, 'up.calendar', false, false, 'team_image');
		$idImage = (int)$idImage;

        if (!($idImage > 0))
		{
			return null;
        }

        return $idImage;
    }

    public static function setUserTeamColor(array $colorTeams): void
    {
        global $USER;
        $idUser = $USER->getId();
        $idTeam = $colorTeams['id'];
        $result = UserTeamTable::update(['ID_USER' => $idUser, 'ID_TEAM' => $idTeam], ['COLOR' => $colorTeams['color']]);

        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
        }
    }
    public static function userIsTeamAdmin($idTeam) :bool
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
}