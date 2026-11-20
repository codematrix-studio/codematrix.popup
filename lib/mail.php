<?php
namespace CodeMatrix\Popup;

use Bitrix\Main\Mail\Event;
use Bitrix\Main\Mail\Internal\EventMessageSiteTable;
use Bitrix\Main\Mail\Internal\EventMessageTable;
use Bitrix\Main\Mail\Internal\EventTypeTable;
use Bitrix\Main\SiteTable;

/**
 * D7-операции почтовой подсистемы модуля.
 *
 * Для шаблонов сохраняется модель Битрикс: одна запись EventMessageTable
 * и отдельные связи с сайтами в EventMessageSiteTable.
 */
class Mail
{
        /**
     * Возвращает шаблоны почтовых событий с указанным кодом.
     *
     * @param string $eventName Код почтового события.
     * @return array<int, array<string, mixed>>
     */
public static function getTemplates($eventName)
    {
        $result = EventMessageTable::getList([
            'select' => ['ID', 'EVENT_NAME'],
            'filter' => ['=EVENT_NAME' => (string)$eventName],
            'order' => ['ID' => 'DESC'],
        ]);

        return $result->fetchAll();
    }

        /**
     * Находит тип почтового события или создаёт его для указанного языка.
     *
     * @param string $eventName Код почтового события.
     * @param string $languageId Идентификатор языка.
     * @param string $name Название типа события.
     * @param string $description Описание типа события.
     * @return int|false ID типа события либо false при ошибке создания.
     */
public static function ensureEventType($eventName, $languageId, $name, $description)
    {
        $eventName = (string)$eventName;
        $languageId = (string)$languageId;

        $exists = EventTypeTable::getList([
            'select' => ['ID'],
            'filter' => [
                '=EVENT_NAME' => $eventName,
                '=LID' => $languageId,
            ],
            'limit' => 1,
        ])->fetch();

        if ($exists) {
            return (int)$exists['ID'];
        }

        $result = EventTypeTable::add([
            'EVENT_NAME' => $eventName,
            'EVENT_TYPE' => 'email',
            'LID' => $languageId,
            'NAME' => (string)$name,
            'DESCRIPTION' => (string)$description,
        ]);

        return $result->isSuccess() ? (int)$result->getId() : false;
    }

        /**
     * Создаёт почтовый шаблон и привязывает его к переданным сайтам из поля LID.
     * При ошибке создания связи удаляет созданный шаблон.
     *
     * @param array $fields Поля шаблона; LID содержит один ID сайта или массив ID.
     * @return int|false ID созданного шаблона либо false при ошибке.
     */
public static function addTemplate(array $fields)
    {
        $siteIds = $fields['LID'] ?? [];
        if (!is_array($siteIds)) {
            $siteIds = [$siteIds];
        }
        $siteIds = array_values(array_unique(array_filter(array_map('strval', $siteIds))));

        unset($fields['LID']);

        $result = EventMessageTable::add($fields);
        if (!$result->isSuccess()) {
            return false;
        }

        $templateId = (int)$result->getId();
        $validSites = [];

        if ($siteIds) {
            $siteResult = SiteTable::getList([
                'select' => ['LID'],
                'filter' => ['@LID' => $siteIds],
            ]);

            while ($site = $siteResult->fetch()) {
                $validSites[] = $site['LID'];
            }
        }

        foreach ($validSites as $siteId) {
            $siteAdd = EventMessageSiteTable::add([
                'EVENT_MESSAGE_ID' => $templateId,
                'SITE_ID' => $siteId,
            ]);

            if (!$siteAdd->isSuccess()) {
                self::deleteTemplate($templateId);
                return false;
            }
        }

        return $templateId;
    }

        /**
     * Удаляет почтовый шаблон и его связи с сайтами.
     *
     * @param int $templateId ID почтового шаблона.
     * @return bool Результат удаления шаблона.
     */
public static function deleteTemplate($templateId)
    {
        $templateId = (int)$templateId;
        if ($templateId <= 0) {
            return false;
        }

        $sites = EventMessageSiteTable::getList([
            'select' => ['EVENT_MESSAGE_ID', 'SITE_ID'],
            'filter' => ['=EVENT_MESSAGE_ID' => $templateId],
        ]);

        while ($site = $sites->fetch()) {
            EventMessageSiteTable::delete([
                'EVENT_MESSAGE_ID' => $templateId,
                'SITE_ID' => $site['SITE_ID'],
            ]);
        }

        return EventMessageTable::delete($templateId)->isSuccess();
    }

        /**
     * Удаляет все почтовые шаблоны с указанным кодом события.
     *
     * @param string $eventName Код почтового события.
     * @return void
     */
public static function deleteTemplates($eventName)
    {
        $templates = EventMessageTable::getList([
            'select' => ['ID'],
            'filter' => ['=EVENT_NAME' => (string)$eventName],
        ])->fetchAll();

        foreach ($templates as $template) {
            self::deleteTemplate((int)$template['ID']);
        }
    }

        /**
     * Удаляет все типы почтовых событий с указанным кодом.
     *
     * @param string $eventName Код почтового события.
     * @return void
     */
public static function deleteEventTypes($eventName)
    {
        $types = EventTypeTable::getList([
            'select' => ['ID'],
            'filter' => ['=EVENT_NAME' => (string)$eventName],
        ])->fetchAll();

        foreach ($types as $type) {
            EventTypeTable::delete((int)$type['ID']);
        }
    }

        /**
     * Отправляет почтовое событие средствами D7 Mail API.
     *
     * @param array $fields Параметры события, включая EVENT_NAME и C_FIELDS.
     * @return mixed Результат Bitrix\\Main\\Mail\\Event::send().
     */
public static function send(array $fields)
    {
        return Event::send($fields);
    }
}
