<?php
namespace Up\Calendar\Model;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\DateField,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Relations\Reference,
    Bitrix\Main\ORM\Query\Join,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class RegularEventTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ID_TEAM int mandatory
 * <li> TITLE string(255) mandatory
 * <li> DATE_TIME datetime mandatory
 * <li> DAY_STEP int mandatory
 * <li> DATE_END date optional
 * </ul>
 *
 * @package Bitrix\Calendar
 **/

class RegularEventTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'up_calendar_regular_event';
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
                    'title' => Loc::getMessage('REGULAR_EVENT_ENTITY_ID_FIELD')
                ]
            ),
            new IntegerField(
                'ID_TEAM',
                [
                    'required' => true,
                    'title' => Loc::getMessage('REGULAR_EVENT_ENTITY_ID_TEAM_FIELD')
                ]
            ),
            new StringField(
                'TITLE',
                [
                    'required' => true,
                    'validation' => [__CLASS__, 'validateTitle'],
                    'title' => Loc::getMessage('REGULAR_EVENT_ENTITY_TITLE_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE_TIME',
                [
                    'required' => true,
                    'title' => Loc::getMessage('REGULAR_EVENT_ENTITY_DATE_TIME_FIELD')
                ]
            ),
            new IntegerField(
                'DAY_STEP',
                [
                    'required' => true,
                    'title' => Loc::getMessage('REGULAR_EVENT_ENTITY_DAY_STEP_FIELD')
                ]
            ),
            new DateField(
                'DATE_END',
                [
                    'title' => Loc::getMessage('REGULAR_EVENT_ENTITY_DATE_END_FIELD')
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
     * Returns validators for TITLE field.
     *
     * @return array
     */
    public static function validateTitle()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }
}