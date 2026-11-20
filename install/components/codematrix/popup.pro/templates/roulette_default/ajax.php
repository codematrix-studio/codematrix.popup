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
$popupId = filter_var($request->get("idPopup"), FILTER_VALIDATE_INT, array(
	"options" => array("min_range" => 1),
));
$settings = ($popupId !== false && \CodeMatrix\Popup\PublicRequestGuard::isPopupRendered($popupId))
	? (new popup())->getSetting($popupId)
	: false;

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
// Проверка уникальности определяется сохранёнными настройками попапа.
$requestedTiming = $request->get("avaliable");
$timing = (string)($props["TIMING"] ?? "");
$rouletteItems = $settings["roulett"] ?? array();
if (!is_array($rouletteItems)) {
	die();
}
$allowedResults = array();

foreach ($rouletteItems as $element) {
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

) {
	die();
}

$requestFingerprint = hash("sha256", serialize(array(
	$rule,
	mb_strtolower(trim($email)),
	$addToTable,
)));
$cachedResponse = \CodeMatrix\Popup\PublicRequestGuard::getCachedResponse("roulette", $popupId, $requestFingerprint);
if ($cachedResponse !== null) {
	if (isset($cachedResponse["response"])) {
		echo $cachedResponse["response"];
	} else {
		echo "coupon_error";
	}
	die();
}

$popup = new popup();
if ($addToTable === "Y") {
	if (($props["EMAIL_ADD2BASE"] ?? "N") !== "Y") {
		die();
	}

	if (($props["EMAIL_NOT_NEW"] ?? "N") === "Y") {
		if (!$popup->searchinMailList($email, $popupId)) {
			echo "not_unique";
			die();
		}
	}
} elseif (($props["EMAIL_ADD2BASE"] ?? "N") === "Y") {
	die();
}

$resultText = $allowedResults[$rule];
if ($rule === "win") {
	$popup->getCoupon($rule, $timing, $email, $popupId, $resultText);
	\CodeMatrix\Popup\PublicRequestGuard::rememberResponse("roulette", $popupId, "", $requestFingerprint);
	$addCookie();
	$context->getResponse()->flush("");
	die();
}

$result = $popup->getCoupon($rule, $timing, $email, $popupId, $resultText);
if ($result === false) {
	echo "coupon_error";
	die();
}
if ($result !== null) {
	\CodeMatrix\Popup\PublicRequestGuard::rememberResponse("roulette", $popupId, (string)$result, $requestFingerprint);
	if ($addToTable === "Y") {
		$popup->insertToMailList($email, "", $popupId);
	}
	$addCookie();
	$context->getResponse()->flush("");
	echo $result;
}

die();
