<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!\Bitrix\Main\Loader::includeModule("codematrix.popup")) {
	die();
}

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$popupId = filter_var($request->get("idPopup"), FILTER_VALIDATE_INT, array(
	"options" => array("min_range" => 1),
));
$ruleId = filter_var($request->get("id"), FILTER_VALIDATE_INT, array(
	"options" => array("min_range" => 1),
));
$settings = ($popupId !== false) ? (new popup())->getSetting($popupId) : false;

if (
	!is_array($settings)
	|| ($settings["view"]["type"] ?? "") !== "coupon"
	|| !is_array($settings["view"]["props"] ?? null)
) {
	die();
}

$props = $settings["view"]["props"];
$configuredRuleId = (int)($props["RULE_ID"] ?? 0);
$configuredTiming = (string)($props["TIMING"] ?? "");
$requestedTiming = $request->get("avaliable");
$email = $request->get("email");
$addToTable = $request->get("addtotable");
// Параметр unique от клиента не является источником настроек: ниже используется конфигурация попапа.

if (
	$ruleId === false
	|| $configuredRuleId <= 0
	|| $ruleId !== $configuredRuleId
	|| !is_string($requestedTiming)
	|| $requestedTiming !== $configuredTiming
	|| !in_array($addToTable, array(null, "Y", "N"), true)
	|| (($props["EMAIL_ADD2BASE"] ?? "N") === "Y" && $addToTable !== "Y")
	|| (($props["EMAIL_ADD2BASE"] ?? "N") !== "Y" && $addToTable === "Y")
) {
	die();
}

if ($addToTable === "Y") {
	if (
		!is_string($email)
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

	$result = $popup->getCoupon($configuredRuleId, $configuredTiming, $email, $popupId);
	if ($result === false) {
		echo "coupon_error";
		die();
	}
	if ($result !== null) {
		$popup->insertToMailList($email, "", $popupId);
		echo $result;
	}
	die();
}

if ($addToTable === null || $addToTable === "N") {
	$emailForCoupon = "";
	if ($email !== null) {
		if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			die();
		}
		$emailForCoupon = $email;
	}

	$result = (new popup())->getCoupon($configuredRuleId, $configuredTiming, $emailForCoupon, $popupId);
	if ($result !== false && $result !== null) {
		echo $result;
	}
}

die();
