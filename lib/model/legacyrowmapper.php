<?php

namespace CodeMatrix\Popup\Model;

/**
 * Приводит строки D7 ORM к формату массивов legacy-кода V1.
 *
 * Bitrix ORM возвращает имена полей в верхнем регистре
 * (ID, SORT, SETTINGS), тогда как старые выборки $DB->Fetch()
 * использовались с ключами в нижнем регистре.
 */
final class LegacyRowMapper
{
    /**
     * Возвращает строку с ключами в нижнем регистре.
     *
     * Пустой результат ORM (false) сохраняется без изменений,
     * чтобы не менять проверки существующего кода.
     *
     * @param array|false|null $row
     * @return array|false|null
     */
    public static function toLegacyRow($row)
    {
        if (!is_array($row)) {
            return $row;
        }

        return array_change_key_case($row, CASE_LOWER);
    }

    /**
     * Безопасно восстанавливает массив из сериализованного значения.
     * Создание PHP-объектов при чтении данных из БД запрещено.
     *
     * @param mixed $value Сериализованное значение.
     * @return array Массив данных либо пустой массив при ошибке или другом типе значения.
     */
    public static function unserializeArray($value)
    {
        if (!is_string($value) || $value === '') {
            return array();
        }

        $result = @unserialize($value, array('allowed_classes' => false));

        return is_array($result) ? $result : array();
    }
}
