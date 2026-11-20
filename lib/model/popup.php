<?php

namespace CodeMatrix\Popup\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

/**
 * ORM-сущность таблицы окон.
 *
 * Имена полей намеренно оставлены в нижнем регистре:
 * это сохраняет ключи массивов, которые возвращает legacy-код V1.
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
            (new IntegerField('id'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('sort'))
                ->configureRequired(),

            (new StringField('active'))
                ->configureSize(1)
                ->configureNullable(true),

            (new StringField('name'))
                ->configureSize(50)
                ->configureNullable(true),

            (new StringField('type'))
                ->configureSize(30)
                ->configureRequired(),

            (new IntegerField('stat_show'))
                ->configureNullable(true),

            (new IntegerField('stat_time'))
                ->configureNullable(true),

            (new IntegerField('stat_action'))
                ->configureNullable(true),

            (new TextField('settings'))
                ->configureRequired(),

            (new IntegerField('cont_iblock'))
                ->configureNullable(true),

            (new IntegerField('cont_posttemplate'))
                ->configureNullable(true),
        ];
    }
}
