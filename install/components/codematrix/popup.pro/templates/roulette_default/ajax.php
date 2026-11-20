<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!\Bitrix\Main\Loader::includeModule("codematrix.popup")) {
	die();
}

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

// Выдача приза изменяет данные магазина и допускается только через POST.
if (!$request->isPost()) {
	http_response_code(405);
	die();
}

$popupId = filter_var($request->getPost("idPopup"), FILTER_VALIDATE_INT, array(
	"options" => array("min_range" => 1),
));

if (
	$popupId === false
	|| !\CodeMatrix\Popup\PublicRequestGuard::isPopupRendered($popupId)
	|| session_status() !== PHP_SESSION_ACTIVE
) {
	die();
}

$popup = new popup();
$settings = $popup->getSetting($popupId);

if (
	!is_array($settings)
	|| ($settings["view"]["type"] ?? "") !== "roulette"
	|| !is_array($settings["view"]["props"] ?? null)
) {
	die();
}

$props = $settings["view"]["props"];
$timing = (string)($props["TIMING"] ?? "");
$addToTable = $request->getPost("addtotable");
$email = $request->getPost("email");
$mode = $request->getPost("mode");

$sendJson = static function (array $data) {
	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	die();
};

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

if (
	!is_string($email)
	|| !filter_var($email, FILTER_VALIDATE_EMAIL)
	|| !is_string($addToTable)
	|| !in_array($addToTable, array("Y", "N"), true)
) {
	$sendJson(array("status" => "error", "code" => "invalid_request"));
}

if (
	($addToTable === "Y" && ($props["EMAIL_ADD2BASE"] ?? "N") !== "Y")
	|| ($addToTable === "N" && ($props["EMAIL_ADD2BASE"] ?? "N") === "Y")
) {
	$sendJson(array("status" => "error", "code" => "invalid_request"));
}

$emailHash = hash("sha256", mb_strtolower(trim($email)));
$drawsKey = "codematrix_popup_roulette_draws";
$completedKey = "codematrix_popup_roulette_completed";

if (!isset($_SESSION[$drawsKey]) || !is_array($_SESSION[$drawsKey])) {
	$_SESSION[$drawsKey] = array();
}
if (!isset($_SESSION[$completedKey]) || !is_array($_SESSION[$completedKey])) {
	$_SESSION[$completedKey] = array();
}

// Первый этап: сервер сам выбирает сектор и выдаёт краткоживущий одноразовый токен.
if ($mode === "draw") {
	$completedAt = (int)($_SESSION[$completedKey][$popupId] ?? 0);
	if ($completedAt > 0 && $completedAt <= time() && time() - $completedAt < 864000000) {
		$sendJson(array("status" => "error", "code" => "already_completed"));
	}
	if ($completedAt > 0) {
		unset($_SESSION[$completedKey][$popupId]);
	}
	if (
		$addToTable === "Y"
		&& ($props["EMAIL_NOT_NEW"] ?? "N") === "Y"
		&& !$popup->searchinMailList($email, $popupId)
	) {
		$sendJson(array("status" => "error", "code" => "not_unique"));
	}

	$pendingDraw = $_SESSION[$drawsKey][$popupId] ?? null;
	if (is_array($pendingDraw)) {
		$createdAt = (int)($pendingDraw["created_at"] ?? 0);
		if ($createdAt > 0 && $createdAt <= time() && time() - $createdAt <= 300) {
			if (
				hash_equals((string)($pendingDraw["email_hash"] ?? ""), $emailHash)
				&& ($pendingDraw["add_to_table"] ?? null) === $addToTable
			) {
				$sendJson(array(
					"status" => "ok",
					"index" => (int)$pendingDraw["index"],
					"token" => (string)$pendingDraw["token"],
				));
			}

			$sendJson(array("status" => "error", "code" => "draw_pending"));
		}

		unset($_SESSION[$drawsKey][$popupId]);
	}

	$rouletteItems = $settings["roulett"] ?? array();
	if (!is_array($rouletteItems)) {
		$sendJson(array("status" => "error", "code" => "invalid_roulette"));
	}

	$rouletteItems = array_values(array_filter($rouletteItems, static function ($element) {
		return is_array($element) && isset($element["rule"]) && is_scalar($element["rule"]);
	}));

	if (!$rouletteItems) {
		$sendJson(array("status" => "error", "code" => "invalid_roulette"));
	}

	try {
		$selectedIndex = random_int(0, count($rouletteItems) - 1);
		$token = bin2hex(random_bytes(32));
	} catch (\Throwable $exception) {
		$sendJson(array("status" => "error", "code" => "draw_failed"));
	}

	$_SESSION[$drawsKey][$popupId] = array(
		"token" => $token,
		"token_hash" => hash("sha256", $token),
		"email_hash" => $emailHash,
		"add_to_table" => $addToTable,
		"index" => $selectedIndex,
		"rule" => (string)$rouletteItems[$selectedIndex]["rule"],
		"text" => (string)($rouletteItems[$selectedIndex]["text"] ?? ""),
		"created_at" => time(),
	);

	$sendJson(array(
		"status" => "ok",
		"index" => $selectedIndex,
		"token" => $token,
	));
}

// Второй этап: сервер выдаёт только тот результат, который был выбран на первом этапе.
if ($mode !== "redeem") {
	$sendJson(array("status" => "error", "code" => "invalid_request"));
}

$token = $request->getPost("token");
if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) {
	$sendJson(array("status" => "error", "code" => "invalid_token"));
}

$requestFingerprint = hash("sha256", serialize(array(
	$token,
	$emailHash,
	$addToTable,
)));
$cachedResponse = \CodeMatrix\Popup\PublicRequestGuard::getCachedResponse(
	"roulette",
	$popupId,
	$requestFingerprint
);

if ($cachedResponse !== null) {
	if (isset($cachedResponse["response"])) {
		header("Content-Type: application/json; charset=UTF-8");
		echo $cachedResponse["response"];
		die();
	}

	$sendJson(array("status" => "error", "code" => "coupon_error"));
}

$pendingDraw = $_SESSION[$drawsKey][$popupId] ?? null;
if (
	!is_array($pendingDraw)
	|| !isset($pendingDraw["token_hash"], $pendingDraw["email_hash"], $pendingDraw["add_to_table"], $pendingDraw["rule"], $pendingDraw["text"], $pendingDraw["created_at"])
	|| !is_string($pendingDraw["token_hash"])
	|| !hash_equals($pendingDraw["token_hash"], hash("sha256", $token))
	|| !hash_equals((string)$pendingDraw["email_hash"], $emailHash)
	|| $pendingDraw["add_to_table"] !== $addToTable
	|| (int)$pendingDraw["created_at"] > time()
	|| time() - (int)$pendingDraw["created_at"] > 300
) {
	$sendJson(array("status" => "error", "code" => "invalid_token"));
}

$rule = (string)$pendingDraw["rule"];
$resultText = (string)$pendingDraw["text"];
$responseData = array(
	"status" => "success",
	"rule" => $rule,
	"text" => $resultText,
	"coupon" => "",
);

if ($rule === "nothing") {
	$addCookie();
} elseif ($rule === "win") {
	$popup->getCoupon($rule, $timing, $email, $popupId, $resultText);
	$addCookie();
} else {
	$result = $popup->getCoupon($rule, $timing, $email, $popupId, $resultText);
	if ($result === false || $result === null) {
		$sendJson(array("status" => "error", "code" => "coupon_error"));
	}

	$responseData["coupon"] = (string)$result;
	if ($addToTable === "Y") {
		$popup->insertToMailList($email, "", $popupId);
	}
	$addCookie();
}

$response = json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if (!is_string($response)) {
	$sendJson(array("status" => "error", "code" => "coupon_error"));
}

\CodeMatrix\Popup\PublicRequestGuard::rememberResponse(
	"roulette",
	$popupId,
	$response,
	$requestFingerprint
);
$_SESSION[$completedKey][$popupId] = time();
unset($_SESSION[$drawsKey][$popupId]);

header("Content-Type: application/json; charset=UTF-8");
echo $response;
