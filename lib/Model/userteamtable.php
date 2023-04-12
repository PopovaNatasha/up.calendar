<?php
namespace Up\Calendar\Model;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\IntegerField;

Loc::loadMessages(__FILE__);

/**
 * Class UserTeamTable
 *
 * Fields:
 * <ul>
 * <li> ID_USER int mandatory
 * <li> ID_TEAM int mandatory
 * </ul>
 *
 * @package Bitrix\Calendar
 **/

class UserTeamTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'up_calendar_user_team';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID_USER',
                [
                    'primary' => true,
                    'title' => Loc::getMessage('USER_TEAM_ENTITY_ID_USER_FIELD')
                ]
            ),
            new IntegerField(
                'ID_TEAM',
                [
                    'primary' => true,
                    'title' => Loc::getMessage('USER_TEAM_ENTITY_ID_TEAM_FIELD')
                ]
            ),
        ];
    }
}