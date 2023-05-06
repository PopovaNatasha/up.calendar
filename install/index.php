<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class up_calendar extends CModule
{
    public $MODULE_ID = 'up.calendar';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && $arModuleVersion['VERSION'] && $arModuleVersion['VERSION_DATE']) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('UP_CALENDAR_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('UP_CALENDAR_MODULE_DESCRIPTION');
    }

    public function installDB(): void
    {
        global $DB;

        $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/local/modules/up.calendar/install/db/install.sql');

        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function uninstallDB($arParams = []): void
    {
        global $DB;

        $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/local/modules/up.calendar/install/db/uninstall.sql');

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installFiles(): void
    {
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/up.calendar/install/components',
            $_SERVER['DOCUMENT_ROOT'] . '/local/components/',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/up.calendar/install/templates',
            $_SERVER['DOCUMENT_ROOT'] . '/local/templates/',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/up.calendar/install/routes',
            $_SERVER['DOCUMENT_ROOT'] . '/local/routes/',
            true,
            true
        );
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/up.calendar/install/js',
            $_SERVER['DOCUMENT_ROOT'] . '/local/js/',
            true,
            true
        );
    }

    public function createAgent(): void
    {
        $nowDate = date('d.m.Y 23:30:00');
        CAgent::AddAgent(
            'Up\Calendar\Agents\AgentStory::userHistory();',
            'up.calendar',
            'N',
            '86400',
            "",
            'Y',
            "$nowDate",
        );
    }

    public function deleteAgent()
    {
        CAgent::RemoveModuleAgents('up.calendar');
    }

    public function uninstallFiles(): void
    {
    }

    public function installEvents(): void
    {
    }

    public function uninstallEvents(): void
    {
    }

    public function doInstall(): void
    {
        global $USER, $APPLICATION;

        if (!$USER->isAdmin()) {
            return;
        }

        $this->installDB();
        $this->installFiles();
        $this->installEvents();
        $this->createAgent();

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('UP_CALENDAR_INSTALL_TITLE'),
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/step.php'
        );
    }

    public function doUninstall(): void
    {
        $this->deleteAgent();
        global $USER, $APPLICATION, $step;

        if (!$USER->isAdmin()) {
            return;
        }

        $step = (int)$step;
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('UP_CALENDAR_UNINSTALL_TITLE'),
                $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/unstep1.php'
            );
        } elseif ($step === 2) {
            $this->uninstallDB();
            $this->uninstallFiles();
            $this->uninstallEvents();

            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('UP_CALENDAR_UNINSTALL_TITLE'),
                $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/unstep2.php'
            );
        }
    }
}
