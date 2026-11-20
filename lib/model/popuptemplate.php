<?php

namespace CodeMatrix\Popup\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

/**
 * ORM-сущность пользовательских шаблонов.
 */
class PopupTemplateTable extends DataManager
{
    /**
     * Возвращает имя таблицы пользовательских шаблонов.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'codematrix_popup_add_templates';
    }

    /**
     * Описывает существующую структуру таблицы.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            (new IntegerField('id'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('name'))
                ->configureSize(50)
                ->configureRequired(),

            (new StringField('type'))
                ->configureSize(50)
                ->configureRequired(),

            (new TextField('template'))
                ->configureRequired(),
        ];
    }
}
