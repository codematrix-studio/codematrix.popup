<?php

namespace CodeMatrix\\Popup;

/**
 * Ограничивает повторное выполнение публичных операций в рамках сессии.
 *
 * Повторный запрос в течение заданного окна получает прежний результат,
 * поэтому повторная отправка формы не создаёт новый купон и не отправляет
 * повторное письмо.
 */
final class PublicRequestGuard
{
    /**
     * Возвращает ранее сохранённый ответ, если запрос повторяется слишком быстро.
     *
     * @param string $action Название публичной операции.
     * @param int $popupId Идентификатор попапа.
     * @param int $windowSeconds Период защиты в секундах.
     * @return array|null Массив с ключом response либо null, если запроса ещё не было.
     */
    public static function getCachedResponse($action, $popupId, $windowSeconds = 60)
    {
        if (session_status() !== PHP_SESSION_ACTIVE || !is_string($action) || $action === '' || (int)$popupId <= 0) {
            return null;
        }

        if (!isset($_SESSION['codematrix_popup_public_requests']) || !is_array($_SESSION['codematrix_popup_public_requests'])) {
            $_SESSION['codematrix_popup_public_requests'] = array();
        }

        $key = self::getKey($action, $popupId);
        $entry = $_SESSION['codematrix_popup_public_requests'][$key] ?? null;

        if (
            !is_array($entry)
            || !isset($entry['time'], $entry['response'])
            || !is_string($entry['response'])
        ) {
            return null;
        }

        $createdAt = (int)$entry['time'];
        if ($createdAt <= 0 || $createdAt > time() || time() - $createdAt >= max(1, (int)$windowSeconds)) {
            unset($_SESSION['codematrix_popup_public_requests'][$key]);
            return null;
        }

        return array('response' => $entry['response']);
    }

    /**
     * Сохраняет успешный ответ для защиты от повторного выполнения операции.
     *
     * @param string $action Название публичной операции.
     * @param int $popupId Идентификатор попапа.
     * @param string $response Ответ, который должен получить повторный запрос.
     * @return void
     */
    public static function rememberResponse($action, $popupId, $response)
    {
        if (
            session_status() !== PHP_SESSION_ACTIVE
            || !is_string($action)
            || $action === ''
            || (int)$popupId <= 0
            || !is_string($response)
        ) {
            return;
        }

        if (!isset($_SESSION['codematrix_popup_public_requests']) || !is_array($_SESSION['codematrix_popup_public_requests'])) {
            $_SESSION['codematrix_popup_public_requests'] = array();
        }

        $now = time();
        foreach ($_SESSION['codematrix_popup_public_requests'] as $key => $entry) {
            if (!is_array($entry) || (int)($entry['time'] ?? 0) < $now - 300) {
                unset($_SESSION['codematrix_popup_public_requests'][$key]);
            }
        }

        $_SESSION['codematrix_popup_public_requests'][self::getKey($action, $popupId)] = array(
            'time' => $now,
            'response' => $response,
        );
    }

    /**
     * Формирует ключ операции в пределах текущей сессии.
     *
     * @param string $action Название публичной операции.
     * @param int $popupId Идентификатор попапа.
     * @return string
     */
    private static function getKey($action, $popupId)
    {
        return hash('sha256', $action . ':' . (int)$popupId);
    }
}
