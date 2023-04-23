<?php

namespace UP\Calendar\Agents;

use Up\Calendar\Model\UserTable;

class AgentStory
{
    public static function userHistory()
    {
        \Bitrix\Main\Loader::includeModule('up.calendar');
        $users = \Bitrix\Main\UserTable::getList([
            'select' => ['id'],
        ])->fetchAll();
        foreach ($users as $user)
        {
            $userTeams = \Up\Calendar\Calendar::getUserTeams($user['ID']);
            if(!$userTeams)
            {
                break;
            }
            $teams = [];
            foreach ($userTeams as $key => $userTeam)
            {
                $teams[$key] = $userTeam['ID_TEAM'];
            }
            $teams = array_values($teams);
            $userEvents =  \Up\Calendar\Calendar::getEventsList($teams);
            $singleEventsToSave = [];
            foreach ($userEvents['events']['singleEvents'] as $singleEvent)
            {
                $nowDate = date('d.m.Y');
                $dayEvent = explode(' ,',$singleEvent['DATE_TIME_FROM'] );
                $dayEvent = $dayEvent[0];
                $dayEvent = substr($dayEvent, 0, strpos($dayEvent, ' '));
                if ($nowDate === $dayEvent)
                {
                    $singleEventsToSave[] = $singleEvent;
                }
            }
            $regularEventsToSave = [];
            foreach ($userEvents['events']['regularEvents'] as $regularEvent)
            {
                $nowDate = date('d.m.Y');
                $dayEvent = explode(' ,',$regularEvent['DATE_TIME_FROM'] );
                $dayEvent = $dayEvent[0];
                $dayEvent = substr($dayEvent, 0, strpos($dayEvent, ' '));
                if ($nowDate === $dayEvent)
                {
                    $regularEventsToSave[] = $regularEvent;
                }
            }

            foreach ($singleEventsToSave as $singleEventToSave)
            {
                $teamTitle = \Up\Calendar\Model\TeamTable::getRowById($singleEventToSave['ID_TEAM']);
                \Up\Calendar\Model\UserStoryTable::createObject()
                    ->setIdUser($user['ID'])
                    ->setTitleTeam($teamTitle['TITLE'])
                    ->setIdTeam($singleEventToSave['ID_TEAM'])
                    ->setDateTimeFrom($singleEventToSave['DATE_TIME_FROM'])
                    ->setTitleEvent($singleEventToSave['TITLE'])
                    ->setDateTimeTo($singleEventToSave['DATE_TIME_TO'])
                    ->save();
            }
            unset($teamTitle);
            foreach ($regularEventsToSave as $regularEventToSave)
            {
                $teamTitle = \Up\Calendar\Model\TeamTable::getRowById($regularEventToSave['ID_TEAM']);
                \Up\Calendar\Model\UserStoryTable::createObject()
                    ->setIdUser($user['ID'])
                    ->setTitleTeam($teamTitle['TITLE'])
                    ->setIdTeam($regularEventToSave['ID_TEAM'])
                    ->setDateTimeFrom($regularEventToSave['DATE_TIME_FROM'])
                    ->setTitleEvent($regularEventToSave['TITLE'])
                    ->setDateTimeTo($regularEventToSave['DATE_TIME_TO'])
                    ->setDayStep($regularEventToSave['DAY_STEP'])
                    ->save();
            }
            unset($teamTitle);
        }
    }
}