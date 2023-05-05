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


    public static function changeEvent($arguments)
    {
        unset($event);
        $idEvent = (int)$arguments['idEvent'];
        $idTeam = (int)$arguments['idTeam'];
        $dateTimeFrom = new DateTime($arguments['dateFrom'], "d.m.Y H:i");
        $dateTimeTo = new DateTime($arguments['dateTo'], "d.m.Y H:i");
		$dateTimeOrigin = new DateTime($arguments['dateFromOrigin'], "d.m.Y H:i");

        if ($arguments['dayStep'] === '')
		{
            $result = EventTable::update($idEvent, [
            	'TITLE' => $arguments['titleEvent'],
            	'DATE_TIME_FROM' => $dateTimeFrom,
            	'DATE_TIME_TO' => $dateTimeTo
            ]);
            if (!$result->isSuccess())
            {
            	return false;
            }
        }
		elseif ($arguments['isAll'] === 'true')
		{
            $result = RegularEventTable::update($idEvent, [
            	'TITLE' => $arguments['titleEvent'],
            	'DATE_TIME_FROM' => $dateTimeFrom,
            	'DATE_TIME_TO' => $dateTimeTo,
            	'DAY_STEP' => (int)$arguments['dayStep'],
            ]);
            if (!$result->isSuccess())
            {
            	return false;
            }
        }
		else
		{
			$changedEvent = ChangedEventTable::getList([
				'select' => ['ID'],
				'filter' => [
					'ID_EVENT' => $idEvent,
					'>DATE_TIME_FROM' => $dateTimeOrigin->add('-1 minutes')->toString(),
					'<=DATE_TIME_FROM' => $dateTimeOrigin->add('+1 minutes')->toString(),
				],
				'count_total' => 1
			]);

			if ($changedEvent->getCount() === 0)
			{
				ChangedEventTable::createObject()
								 ->setTitle($arguments['titleEvent'])
								 ->setIdTeam($idTeam)
								 ->setDateTimeFrom($dateTimeFrom)
								 ->setDateTimeTo($dateTimeTo)
								 ->setIdEvent($arguments['idEvent'])
								 ->save();
			}
			else
			{
				$changedEvent->fetchObject()
							 ->setTitle($arguments['titleEvent'])
							 ->setIdTeam($idTeam)
							 ->setDateTimeFrom($dateTimeFrom)
							 ->setDateTimeTo($dateTimeTo)
							 ->setIdEvent($arguments['idEvent'])
							 ->save();
			}
        }
        return true;
    }

    public static function deleteEvent($arguments)
    {
        unset($events);
		$idEvent = (int)$arguments['idEvent'];
		$idTeam = (int)$arguments['idTeam'];
		$dateTimeFrom = new DateTime($arguments['dateFrom'], "d.m.Y H:i");
		$dateTimeTo = new DateTime($arguments['dateTo'], "d.m.Y H:i");

        if ($arguments['dayStep'] === '')
		{
            EventTable::delete(['ID' => $idEvent]);
        }
		elseif ($arguments['isAll'] === 'true')
		{
            RegularEventTable::delete(['ID' => $idEvent]);

            $events = ChangedEventTable::getList([
                'select' => ['ID'],
                'filter' => [
                    'ID_EVENT' => $idEvent,
                ]
            ])->fetchAll();

            if ($events) {
                foreach ($events as $event) {
                    ChangedEventTable::delete($event['ID']);
                }
            }
        }
		else
		{
			ChangedEventTable::createObject()
							 ->setTitle($arguments['titleEvent'])
							 ->setIdTeam($idTeam)
							 ->setDateTimeFrom($dateTimeFrom)
							 ->setDateTimeTo($dateTimeTo)
							 ->setIdEvent($idEvent)
							 ->setDeleted(true)
							 ->save();
		}
		return true;
    }
}