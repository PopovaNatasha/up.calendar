<?php
namespace Up\Calendar\Model;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class UserStoryTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ID_USER int mandatory
 * <li> DATE_TIME datetime mandatory
 * <li> ID_TEAM int mandatory
 * <li> TITLE_TEAM string(255) mandatory
 * <li> TITLE_EVENT string(255) mandatory
 * </ul>
 *
 * @package Bitrix\Calendar
 **/

class UserStoryTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'up_calendar_user_story';
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
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => Loc::getMessage('USER_STORY_ENTITY_ID_FIELD')
                ]
            ),
            new IntegerField(
                'ID_USER',
                [
                    'required' => true,
                    'title' => Loc::getMessage('USER_STORY_ENTITY_ID_USER_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE_TIME',
                [
                    'required' => true,
                    'title' => Loc::getMessage('USER_STORY_ENTITY_DATE_TIME_FIELD')
                ]
            ),
            new IntegerField(
                'ID_TEAM',
                [
                    'required' => true,
                    'title' => Loc::getMessage('USER_STORY_ENTITY_ID_TEAM_FIELD')
                ]
            ),
            new StringField(
                'TITLE_TEAM',
                [
                    'required' => true,
                    'validation' => [__CLASS__, 'validateTitleTeam'],
                    'title' => Loc::getMessage('USER_STORY_ENTITY_TITLE_TEAM_FIELD')
                ]
            ),
            new StringField(
                'TITLE_EVENT',
                [
                    'required' => true,
                    'validation' => [__CLASS__, 'validateTitleEvent'],
                    'title' => Loc::getMessage('USER_STORY_ENTITY_TITLE_EVENT_FIELD')
                ]
            ),
            new Reference(
                'TEAM',
                TeamTable::class,
                Join::on('this.ID_TEAM', 'ref.ID')
            ),
        ];
    }

    /**
     * Returns validators for TITLE_TEAM field.
     *
     * @return array
     */
    public static function validateTitleTeam()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for TITLE_EVENT field.
     *
     * @return array
     */
    public static function validateTitleEvent()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }
}