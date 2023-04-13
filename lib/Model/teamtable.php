<?php
namespace Up\Calendar\Model;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Relations\Reference,
    Bitrix\Main\ORM\Query\Join,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class TeamTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TITLE string(255) mandatory
 * <li> DESCRIPTON string(255) optional
 * <li> ID_ADMIN int mandatory
 * <li> PERSONAL_PHOTO int optional
 * <li> IS_PRIVATE unknown optional
 * </ul>
 *
 * @package Bitrix\Calendar
 **/

class TeamTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'up_calendar_team';
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
                    'title' => Loc::getMessage('TEAM_ENTITY_ID_FIELD')
                ]
            ),
            new StringField(
                'TITLE',
                [
                    'required' => true,
                    'validation' => [__CLASS__, 'validateTitle'],
                    'title' => Loc::getMessage('TEAM_ENTITY_TITLE_FIELD')
                ]
            ),
            new StringField(
                'DESCRIPTION',
                [
                    'validation' => [__CLASS__, 'validateDescripton'],
                    'title' => Loc::getMessage('TEAM_ENTITY_DESCRIPTION_FIELD')
                ]
            ),
            new IntegerField(
                'ID_ADMIN',
                [
                    'required' => true,
                    'title' => Loc::getMessage('TEAM_ENTITY_ID_ADMIN_FIELD')
                ]
            ),
            new IntegerField(
                'PERSONAL_PHOTO',
                [
                    'title' => Loc::getMessage('TEAM_ENTITY_PERSONAL_PHOTO_FIELD')
                ]
            ),
            new StringField(
            'IS_PRIVATE',
				[
                    'title' => Loc::getMessage('TEAM_ENTITY_IS_PRIVATE_FIELD')
                ]
			),
            new Reference(
                'ADMIN',
                UserTable::class,
                Join::on('this.ID_ADMIN', 'ref.ID')
            ),
            new Reference(
                'TEAM_PHOTO',
                FileTable::class,
                Join::on('this.PERSONAL_PHOTO', 'ref.ID')
            ),
			new Reference(
				'USER',
				UserTeamTable::class,
				Join::on('this.ID', 'ref.ID_TEAM')
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

    /**
     * Returns validators for DESCRIPTON field.
     *
     * @return array
     */
    public static function validateDescripton()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }
}