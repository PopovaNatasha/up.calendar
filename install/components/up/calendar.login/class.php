<?php



class CalendarLoginComponent extends CBitrixComponent
{
    public function executeComponent()
    {
       // \Bitrix\Main\Loader::includeModule('up.tasks');
        $this->fetchTask();
        $this->includeComponentTemplate();
    }

    protected function fetchTask()
    {

    }
}