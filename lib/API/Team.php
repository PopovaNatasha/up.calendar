<?php

namespace Up\Calendar\API;

use Up\Calendar\Model\TeamTable,
    Up\Calendar\Model\UserTeamTable,
    Bitrix\Main\UI\PageNavigation;

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

    public static function deleteTeam($id): void
    {
        TeamTable::delete($id);
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
            if(!$idImage)
            {
                throw new \Exception('Invalid type file');
            }
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

        if (!\CFile::IsImage($arImage['name']))
        {
            return false;
        }
        $idImage = \CFile::SaveFile($arImage, 'up.calendar', false, false, 'team_image');

        if (!((int)$idImage > 0)) {
            throw new \Exception('Failed to save file');
        }

        return (int)$idImage;
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
}