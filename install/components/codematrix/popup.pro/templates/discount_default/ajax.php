<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!\Bitrix\Main\Loader::includeModule("codematrix.popup")) {
	die();
}

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

// Выдача купона изменяет данные магазина и допускается только через POST.
if (!$request->isPost()) {
	http_response_code(405);
	die();
}
$popupId = filter_var($request->getPost("idPopup"), FILTER_VALIDATE_INT, array(
	"options" => array("min_range" => 1),
));
$settings = ($popupId !== false && \CodeMatrix\Popup\PublicRequestGuard::isPopupRendered($popupId))
	? (new popup())->getSetting($popupId)
	: false;

if (
	!is_array($settings)
	|| ($settings["view"]["type"] ?? "") !== "discount"
	|| !is_array($settings["view"]["props"] ?? null)
) {
	die();
}

$props = $settings["view"]["props"];
$ruleValue = $props["RULE_ID"] ?? null;
$ruleId = (is_string($ruleValue) || is_int($ruleValue))
	? filter_var($ruleValue, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))
	: false;
$groupValue = $props["USER_GROUP"] ?? null;
$groupId = (is_string($groupValue) || is_int($groupValue))
	? filter_var($groupValue, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))
	: false;
if ($groupId === false || !\Bitrix\Main\GroupTable::getById($groupId)->fetch()) {
	// Неизвестная или удалённая группа не должна ломать выдачу карты.
	$groupId = 0;
}
$maskValue = $props["DISCOUNT_MASK"] ?? "";
$mask = is_string($maskValue) ? $maskValue : null;
$email = $request->getPost("email");
$addToTable = $request->getPost("addtotable");
// Проверка уникальности определяется сохранёнными настройками, а не параметром клиента.

$consentRequired = ($props["USE_CONSENT_SHOW"] ?? "N") === "Y"
	&& !empty($props["CONSENT_LIST"]);
$consentValue = $request->getPost("use_consent");

if ($consentRequired && $consentValue !== "Y") {
	echo "consent_required";
	die();
}

if (
	$ruleId === false
	|| $mask === null
	|| mb_strlen($mask) > 64
	|| !in_array($addToTable, array("Y"), true)
	|| !is_string($email)
	|| !filter_var($email, FILTER_VALIDATE_EMAIL)
	|| ($props["EMAIL_ADD2BASE"] ?? "N") !== "Y"
) {
	die();
}

$requestFingerprint = hash("sha256", mb_strtolower(trim($email)));
$cachedResponse = \CodeMatrix\Popup\PublicRequestGuard::getCachedResponse("discount", $popupId, $requestFingerprint);
if ($cachedResponse !== null) {
	if (isset($cachedResponse["response"])) {
		echo $cachedResponse["response"];
	} else {
		echo "coupon_error";
	}
	die();
}

$popup = new popup();

// Сохраняем подтверждение согласия в журнале Битрикс до обработки персональных данных.
if ($consentRequired) {
	$consentData = array();
	$referer = $_SERVER["HTTP_REFERER"] ?? "";
	if (is_string($referer) && $referer !== "") {
		$consentData["URL"] = $referer;
	}
	if (!class_exists("\\Bitrix\\Main\\UserConsent\\Consent")) {
		echo "coupon_error";
		die();
	}

	try {
		$consentRecordId = \Bitrix\Main\UserConsent\Consent::addByContext(
			$props["CONSENT_LIST"],
			"codematrix/popup",
			$popupId,
			$consentData
		);
		if (!$consentRecordId) {
			echo "coupon_error";
			die();
		}
	} catch (\Throwable $exception) {
		echo "coupon_error";
		die();
	}
}

if (($props["EMAIL_NOT_NEW"] ?? "N") === "Y") {
	if (!$popup->searchinMailList($email, $popupId)) {
		echo "not_unique";
		die();
	}
}

global $USER;
if (!$USER->IsAuthorized()) {
	$userRecord = \Bitrix\Main\UserTable::getList(array(
		"select" => array("ID"),
		"filter" => array("=EMAIL" => $email),
		"order" => array("ID" => "DESC"),
		"limit" => 1,
	))->fetch();

	if ($userRecord) {
		// Нельзя входить в существующую учётную запись только по адресу email.
		echo "auth_required";
		die();
	}

	// SimpleRegister возвращает массив сообщений, а не булев результат.
	// Фактический успех регистрации подтверждаем по авторизованной сессии и ID.
	$registerResult = $USER->SimpleRegister($email);
	$userId = (int)$USER->GetID();
	if (!is_array($registerResult) || !$USER->IsAuthorized() || $userId <= 0) {
		echo "coupon_error";
		die();
	}

	$name = $request->getPost("NAME");
	$lastName = $request->getPost("LAST_NAME");
	$phone = $request->getPost("PHONE");
	$userFields = array(
		"NAME" => is_string($name) ? $name : "",
		"LAST_NAME" => is_string($lastName) ? $lastName : "",
		"PERSONAL_PHONE" => is_string($phone) ? $phone : "",
	);
	// Обновляем профиль через текущий объект пользователя, чтобы почтовые события
	// ниже получили актуальные имя и фамилию из авторизованной сессии.
	$USER->Update($userId, $userFields);
}

$result = $popup->getCoupon($ruleId, "infinite", $email, $popupId, "", $mask);
if ($result === false) {
	echo "coupon_error";
	die();
}
if ($result !== null) {
	// Сохраняем ответ до дополнительных действий, чтобы повтор не создавал новый купон.
	\CodeMatrix\Popup\PublicRequestGuard::rememberResponse("discount", $popupId, (string)$result, $requestFingerprint);

	// Группу назначаем только после успешного создания купона.
	$userId = (int)$USER->GetID();
	if ($userId > 0 && $groupId > 0) {
		$groupLink = \Bitrix\Main\UserGroupTable::getList(array(
			"select" => array("USER_ID"),
			"filter" => array(
				"=USER_ID" => $userId,
				"=GROUP_ID" => $groupId,
			),
			"limit" => 1,
		))->fetch();

		if (!$groupLink) {
			\Bitrix\Main\UserGroupTable::add(array(
				"USER_ID" => $userId,
				"GROUP_ID" => $groupId,
			));
		}
	}

	$popup->insertToMailList($email, "", $popupId);

	$context = \Bitrix\Main\Application::getInstance()->getContext();
	$cookie = new \Bitrix\Main\Web\Cookie(
		"codematrixPopupFilling_" . $popupId,
		"Y",
		time() + 864000000
	);
	$cookie->setHttpOnly(false);
	$context->getResponse()->addCookie($cookie);
	$context->getResponse()->flush("");

	echo $result;
}

die();
