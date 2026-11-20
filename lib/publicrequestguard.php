<?php

namespace CodeMatrix\Popup;

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
     * Проверяет, выдавался ли HTML попапа текущей PHP-сессии в допустимый период.
     *
     * @param int $popupId Идентификатор попапа.
     * @param int $maxAgeSeconds Максимальный возраст маркера в секундах.
     * @return bool
     */
    public static function isPopupRendered($popupId, $maxAgeSeconds = 86400)
    {
        if (session_status() !== PHP_SESSION_ACTIVE || (int)$popupId <= 0) {
            return false;
        }

        if (!isset($_SESSION['codematrix_popup_rendered']) || !is_array($_SESSION['codematrix_popup_rendered'])) {
            return false;
        }

        $renderedAt = (int)($_SESSION['codematrix_popup_rendered'][(int)$popupId] ?? 0);
        return $renderedAt > 0
            && $renderedAt <= time()
            && time() - $renderedAt <= max(1, (int)$maxAgeSeconds);
    }

    /**
     * Возвращает ранее сохранённый ответ, если запрос повторяется слишком быстро.
     *
     * @param string $action Название публичной операции.
     * @param int $popupId Идентификатор попапа.
     * @param string $fingerprint Хеш значимых параметров запроса без сохранения персональных данных.
     * @param int $windowSeconds Период защиты в секундах.
     * @return array|null Массив с response для точного повтора, blocked для другого запроса в период блокировки либо null.
     */
    public static function getCachedResponse($action, $popupId, $fingerprint = '', $windowSeconds = 60)
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
            || !isset($entry['time'], $entry['response'], $entry['fingerprint'])
            || !is_string($entry['response'])
            || !is_string($entry['fingerprint'])
        ) {
            return null;
        }

        $createdAt = (int)$entry['time'];
        if ($createdAt <= 0 || $createdAt > time() || time() - $createdAt >= max(1, (int)$windowSeconds)) {
            unset($_SESSION['codematrix_popup_public_requests'][$key]);
            return null;
        }

        if (!hash_equals($entry['fingerprint'], (string)$fingerprint)) {
            return array('blocked' => true);
        }

        return array('response' => $entry['response']);
    }

    /**
     * Сохраняет успешный ответ для защиты от повторного выполнения операции.
     *
     * @param string $action Название публичной операции.
     * @param int $popupId Идентификатор попапа.
     * @param string $response Ответ, который должен получить повторный запрос.
     * @param string $fingerprint Хеш значимых параметров исходного запроса.
     * @return void
     */
    public static function rememberResponse($action, $popupId, $response, $fingerprint = '')
    {
        if (
            session_status() !== PHP_SESSION_ACTIVE
            || !is_string($action)
            || $action === ''
            || (int)$popupId <= 0
            || !is_string($response)
            || !is_string($fingerprint)
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
            'fingerprint' => $fingerprint,
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
