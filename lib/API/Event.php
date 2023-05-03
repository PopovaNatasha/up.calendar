<?php

namespace Up\Calendar\API;


use Bitrix\Main\Type\DateTime,
    Up\Calendar\Model\EventTable,
    Up\Calendar\Model\RegularEventTable;
use Bitrix\Translate\Controller\Index\CollectLangPath;
use Up\Calendar\Model\ChangedEventTable;
use Up\Calendar\Model\UserStoryTable;

class Event
{
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

        $changedEvents = ChangedEventTable::getList([
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
            'changedEvents' => $changedEvents,
            'userStoryEvents' => $userStoryEvents,
        ]];
    }


    public static function changeEvent($arguments): bool
    {
        unset($event);
        $idEvent = (int)$arguments['idEvent'];
        $idTeam = (int)$arguments['idTeam'];
        $dateTimeFrom = new DateTime($arguments['dateFrom'], "d.m.Y H:i");
        $dateTimeTo = new DateTime($arguments['dateTo'], "d.m.Y H:i");

        if ($arguments['dayStep'] === '') {
            // $result = EventTable::update($idEvent, [
            // 	'TITLE' => $arguments['titleEvent'],
            // 	'DATE_TIME_FROM' => $dateTimeFrom,
            // 	'DATE_TIME_TO' => $dateTimeTo
            // ]);
            // if (!$result->isSuccess())
            // {
            // 	return false;
            // }
            $event = EventTable::getByPrimary(['ID' => $idEvent])->fetchObject();
            $event->setTitle($arguments['titleEvent'])
                ->setDateTimeFrom($dateTimeFrom)
                ->setDateTimeTo($dateTimeTo)
                ->save();
        } elseif ($arguments['isAll'] === 'true') {
            // $result = RegularEventTable::update($idEvent, [
            // 	'TITLE' => $arguments['titleEvent'],
            // 	'DATE_TIME_FROM' => $dateTimeFrom,
            // 	'DATE_TIME_TO' => $dateTimeTo,
            // 	'DAY_STEP' => $arguments['dayStep'],
            // ]);
            // if (!$result->isSuccess())
            // {
            // 	return false;
            // }
            $event = RegularEventTable::getByPrimary(['ID' => $idEvent])->fetchObject();
			$IdLastChangedEvent = $event->get('ID_LAST_CHANGED_EVENT');
			if ($IdLastChangedEvent)
			{RegularEventTable::update($IdLastChangedEvent, [
					'ID_TEAM' => $idTeam,
					'TITLE' => $arguments['titleEvent'],
					'DATE_TIME_FROM' => $dateTimeFrom,
					'DATE_TIME_TO' => $dateTimeTo,
					'DAY_STEP' => $arguments['dayStep'],
				]);
				$event->setDateEnd($dateTimeFrom)->save();
			}
			else
			{
				$changedEvent = RegularEventTable::createObject()
								 ->setTitle($arguments['titleEvent'])
								 ->setIdTeam($idTeam)
								 ->setDateTimeFrom($dateTimeFrom)
								 ->setDateTimeTo($dateTimeTo)
								 ->setDayStep($arguments['dayStep'])
								 ->save();
				$idChangedEvent = $changedEvent->getId();

				$event->setDateEnd($dateTimeFrom)
					  ->setIdLastChangedEvent($idChangedEvent)
					  ->save();
			}
        } else {
            ChangedEventTable::createObject()
                ->setTitle($arguments['titleEvent'])
                ->setIdTeam($idTeam)
                ->setDateTimeFrom($dateTimeFrom)
                ->setDateTimeTo($dateTimeTo)
                ->setIdEvent($arguments['idEvent'])
                ->save();
        }
        return true;
    }

    public static function deleteEvent($arguments)
    {
        unset($events);
        if (!$arguments['day_step']) {
            EventTable::delete(['ID' => (int)$arguments['id']]);
        } else {
            RegularEventTable::delete(['ID' => (int)$arguments['id']]);

            $events = ChangedEventTable::getList([
                'select' => ['ID'],
                'filter' => [
                    'ID_EVENT' => (int)$arguments['id'],
                ]
            ])->fetchAll();

            if ($events) {
                foreach ($events as $event) {
                    ChangedEventTable::delete($event['ID']);
                }
            }
        }
    }
}