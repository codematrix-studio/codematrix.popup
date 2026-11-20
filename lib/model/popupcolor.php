<?php

namespace CodeMatrix\Popup\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

/**
 * ORM-сущность пользовательских цветовых тем.
 */
class PopupColorTable extends DataManager
{
    /**
     * Возвращает имя таблицы цветовых тем.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'codematrix_popup_add_colors';
    }

    /**
     * Описывает существующую структуру таблицы.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('TEMPLATE'))
                ->configureSize(50)
                ->configureRequired(),

            (new StringField('NAME'))
                ->configureSize(50)
                ->configureNullable(true),
        ];
    }
}
