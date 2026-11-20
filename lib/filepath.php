<?php

namespace CodeMatrix\\Popup;

use Bitrix\\Main\\FileTable;

/**
 * Предоставляет совместимые с прежним API пути к загруженным файлам.
 */
final class FilePath
{
    /**
     * Возвращает публичный путь к файлу по его идентификатору.
     *
     * @param int|string $fileId Идентификатор записи файла.
     * @return string|false Путь относительно корня сайта либо false, если файл не найден.
     */
    public static function getPath($fileId)
    {
        $fileId = filter_var($fileId, FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1),
        ));

        if ($fileId === false) {
            return false;
        }

        $file = FileTable::getList(array(
            'select' => array('ID', 'SUBDIR', 'FILE_NAME'),
            'filter' => array('=ID' => $fileId),
            'limit' => 1,
        ))->fetch();

        if (
            !is_array($file)
            || empty($file['FILE_NAME'])
        ) {
            return false;
        }

        $subDirectory = trim((string)($file['SUBDIR'] ?? ''), '/\\\\');
        $fileName = ltrim((string)$file['FILE_NAME'], '/\\\\');

        if ($fileName === '') {
            return false;
        }

        return '/upload/' . ($subDirectory !== '' ? $subDirectory . '/' : '') . $fileName;
    }
}
