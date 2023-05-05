<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;

function __CalendarMigrator(int $nextVersion, callable $callback)
{
    global $DB;
    $moduleId = 'up.calendar';

    if (!ModuleManager::isModuleInstalled($moduleId)) {
        return;
    }

    $currentVersion = intval(Option::get($moduleId, '~database_schema_version', 0));

    if ($currentVersion < $nextVersion) {
        include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/update_class.php');
        $updater = new \CUpdater();
        $updater->Init('', 'mysql', '', '', $moduleId, 'DB');

        $callback($updater, $DB, 'mysql');
        Option::set($moduleId, '~database_schema_version', $nextVersion);
    }
}


__CalendarMigrator(2, function ($updater, $DB) {
    if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_team')) {
        $DB->query("ALTER TABLE up_calendar_team ADD INVITE_LINK varchar(255)");
    }
});

__CalendarMigrator(3, function ($updater, $DB) {
    if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_event')) {
        $DB->query("ALTER TABLE up_calendar_event CHANGE COLUMN DATE_TIME DATE_TIME_FROM DATETIME");
        $DB->query("ALTER TABLE up_calendar_event ADD DATE_TIME_TO DATETIME");
    }
});

__CalendarMigrator(4, function ($updater, $DB) {
    if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_regular_event')) {
        $DB->query("ALTER TABLE up_calendar_regular_event CHANGE COLUMN DATE_TIME DATE_TIME_FROM DATETIME");
        $DB->query("ALTER TABLE up_calendar_regular_event ADD DATE_TIME_TO DATETIME");
    }
});

__CalendarMigrator(5, function ($updater, $DB) {
    if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_changed_event')) {
        $DB->query("ALTER TABLE up_calendar_changed_event CHANGE COLUMN DATE_TIME DATE_TIME_FROM DATETIME");
        $DB->query("ALTER TABLE up_calendar_changed_event ADD DATE_TIME_TO DATETIME");
    }
});

__CalendarMigrator(6, function ($updater, $DB) {
    if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_user_story')) {
        $DB->query("ALTER TABLE up_calendar_user_story CHANGE COLUMN DATE_TIME DATE_TIME_FROM DATETIME");
        $DB->query("ALTER TABLE up_calendar_user_story ADD DATE_TIME_TO DATETIME");
        $DB->query("ALTER TABLE up_calendar_user_story ADD DAY_STEP int");
    }
});

__CalendarMigrator(7, function ($updater, $DB) {
    if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_user_team')) {
        $DB->query("ALTER TABLE up_calendar_user_team ADD COLOR VARCHAR(7)");
    }
});

__CalendarMigrator(8, function ($updater, $DB) {
    if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_changed_event')) {
        $DB->query("ALTER TABLE up_calendar_changed_event ADD ID_EVENT INT");
    }
});

__CalendarMigrator(9, function ($updater, $DB) {
	if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_regular_event')) {
		$DB->query("ALTER TABLE up_calendar_regular_event CHANGE COLUMN DATE_END DATE_END DATETIME");
	}
});

__CalendarMigrator(10, function ($updater, $DB) {
	if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_regular_event')) {
		$DB->query("ALTER TABLE up_calendar_regular_event ADD ID_ORIGINAL_EVENT INT");
	}
});

__CalendarMigrator(11, function ($updater, $DB) {
	if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_regular_event')) {
		$DB->query("ALTER TABLE up_calendar_regular_event CHANGE COLUMN ID_ORIGINAL_EVENT ID_LAST_CHANGED_EVENT INT");
	}
});

__CalendarMigrator(12, function ($updater, $DB) {
	if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_changed_event')) {
		$DB->query("ALTER TABLE up_calendar_changed_event ADD DELETED BINARY DEFAULT 0");
	}
	if ($updater->CanUpdateDatabase() && $updater->TableExists('up_calendar_regular_event')) {
		$DB->query("ALTER TABLE up_calendar_regular_event DROP ID_LAST_CHANGED_EVENT");
	}
});