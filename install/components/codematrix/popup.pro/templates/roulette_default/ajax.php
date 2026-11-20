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
	|| ($settings["view"]["type"] ?? "") !== "roulette"
	|| !is_array($settings["view"]["props"] ?? null)
) {
	die();
}

$props = $settings["view"]["props"];
$rule = $request->get("id");
$email = $request->get("email");
$addToTable = $request->get("addtotable");
$unique = $request->get("unique");
$requestedTiming = $request->get("avaliable");
$timing = (string)($props["TIMING"] ?? "");
$allowedResults = array();

foreach (($settings["roulett"] ?? array()) as $element) {
	if (is_array($element) && isset($element["rule"])) {
		$allowedResults[(string)$element["rule"]] = (string)($element["text"] ?? "");
	}
}

$context = \Bitrix\Main\Application::getInstance()->getContext();
$addCookie = static function () use ($context, $popupId) {
	$cookie = new \Bitrix\Main\Web\Cookie(
		"codematrixPopupFilling_" . $popupId,
		"Y",
		time() + 864000000
	);
	$cookie->setDomain($context->getServer()->getHttpHost());
	$cookie->setHttpOnly(false);
	$context->getResponse()->addCookie($cookie);
};

if ($request->get("nothing") !== null) {
	$addCookie();
	$context->getResponse()->flush("");
	die();
}

if (
	!is_string($rule)
	|| !array_key_exists($rule, $allowedResults)
	|| !is_string($requestedTiming)
	|| $requestedTiming !== $timing
	|| !is_string($email)
	|| !filter_var($email, FILTER_VALIDATE_EMAIL)
	|| !in_array($addToTable, array("Y", "N"), true)
	|| !in_array($unique, array("Y", "N", null), true)
) {
	die();
}

$popup = new popup();
if ($addToTable === "Y") {
	if (($props["EMAIL_ADD2BASE"] ?? "N") !== "Y") {
		die();
	}

	if (($props["EMAIL_NOT_NEW"] ?? "N") === "Y" && $unique === "Y") {
		if (!$popup->searchinMailList($email, $popupId)) {
			echo "not_unique";
			die();
		}
	}
}

$resultText = $allowedResults[$rule];
$result = $popup->getCoupon($rule, $timing, $email, $popupId, $resultText);
if ($result !== false && $result !== null) {
	if ($addToTable === "Y") {
		$popup->insertToMailList($email, "", $popupId);
	}
	$addCookie();
	$context->getResponse()->flush("");
	echo $result;
}

die();
