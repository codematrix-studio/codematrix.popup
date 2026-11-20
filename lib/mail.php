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
    public static function getTemplates($eventName)
    {
        $result = EventMessageTable::getList([
            'select' => ['ID', 'EVENT_NAME'],
            'filter' => ['=EVENT_NAME' => (string)$eventName],
            'order' => ['ID' => 'DESC'],
        ]);

        return $result->fetchAll();
    }

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

    public static function send(array $fields)
    {
        return Event::send($fields);
    }
}
