<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!\Bitrix\Main\Loader::includeModule("codematrix.popup")) {
	die();
}

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$popupId = filter_var($request->get("idPopup"), FILTER_VALIDATE_INT, array(
	"options" => array("min_range" => 1),
));
$settings = ($popupId !== false) ? (new popup())->getSetting($popupId) : false;

if (
	!is_array($settings)
	|| ($settings["view"]["type"] ?? "") !== "discount"
	|| !is_array($settings["view"]["props"] ?? null)
) {
	die();
}

$props = $settings["view"]["props"];
$ruleId = (int)($props["RULE_ID"] ?? 0);
$groupId = (int)($props["USER_GROUP"] ?? 0);
$mask = (string)($props["DISCOUNT_MASK"] ?? "");
$email = $request->get("email");
$addToTable = $request->get("addtotable");
// Проверка уникальности определяется сохранёнными настройками, а не параметром клиента.

if (
	$ruleId <= 0
	|| !in_array($addToTable, array("Y"), true)
	|| !is_string($email)
	|| !filter_var($email, FILTER_VALIDATE_EMAIL)
	|| ($props["EMAIL_ADD2BASE"] ?? "N") !== "Y"
) {
	die();
}

$popup = new popup();
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

	$user = new CUser();
	$registerResult = $USER->SimpleRegister($email);
	if (!$registerResult || !$USER->IsAuthorized()) {
		die();
	}

	$name = $request->get("NAME");
	$lastName = $request->get("LAST_NAME");
	$phone = $request->get("PHONE");
	$userFields = array(
		"NAME" => is_string($name) ? $name : "",
		"LAST_NAME" => is_string($lastName) ? $lastName : "",
		"PERSONAL_PHONE" => is_string($phone) ? $phone : "",
	);
	$user->Update((int)$USER->GetID(), $userFields);
}

$result = $popup->getCoupon($ruleId, "infinite", $email, $popupId, "", $mask);
if ($result === false) {
	echo "coupon_error";
	die();
}
if ($result !== null) {
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
	$cookie->setDomain($context->getServer()->getHttpHost());
	$cookie->setHttpOnly(false);
	$context->getResponse()->addCookie($cookie);
	$context->getResponse()->flush("");

	echo $result;
}

die();
