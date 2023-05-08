<?php

namespace Up\Calendar\Services;

use Bitrix\Main\SystemException,
	Bitrix\Main\Type\DateTime;

class Schedule
{
	public static function splitDateIntoTwo(string $date): ?array
	{
		$eventDates = explode(' - ', $date);
		$eventPeriod = [];
		try
		{
			$eventPeriod['start'] = new DateTime($eventDates[0], "d.m.Y H:i");
			$eventPeriod['end'] = new DateTime($eventDates[1], "d.m.Y H:i");
		}
		catch (SystemException $e)
		{
			return false;
		}

		return $eventPeriod;
	}
}