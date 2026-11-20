<?php

namespace CodeMatrix\Popup\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

/**
 * ORM-сущность таблицы окон.
 *
 * Bitrix ORM нормализует имена полей сущности в верхний регистр.
 * При чтении для legacy-кода используется LegacyRowMapper, который
 * возвращает ключи массивов в нижнем регистре.
 */
class PopupTable extends DataManager
{
    /**
     * Возвращает имя таблицы окон.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'codematrix_popup';
    }

    /**
     * Описывает существующую структуру таблицы без её изменения.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('SORT'))
                ->configureRequired(),

            (new StringField('ACTIVE'))
                ->configureSize(1)
                ->configureNullable(true),

            (new StringField('NAME'))
                ->configureSize(50)
                ->configureNullable(true),

            (new StringField('TYPE'))
                ->configureSize(30)
                ->configureRequired(),

            (new IntegerField('STAT_SHOW'))
                ->configureNullable(true),

            (new IntegerField('STAT_TIME'))
                ->configureNullable(true),

            (new IntegerField('STAT_ACTION'))
                ->configureNullable(true),

            (new TextField('SETTINGS'))
                ->configureRequired(),

            (new IntegerField('CONT_IBLOCK'))
                ->configureNullable(true),

            (new IntegerField('CONT_POSTTEMPLATE'))
                ->configureNullable(true),
        ];
    }
}
