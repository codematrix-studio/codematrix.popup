<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$moduleLoaded = \Bitrix\Main\Loader::includeModule("codematrix.popup");
$idPopup = filter_var(
	$request->get("id_popup"),
	FILTER_VALIDATE_INT,
	array("options" => array("min_range" => 1))
);

// Endpoint закреплён за своим шаблоном и принимает только попапы соответствующего типа.
$popupSettings = ($moduleLoaded && $idPopup !== false)
	? (new popup())->getSetting($idPopup)
	: false;

if (is_array($popupSettings) && ($popupSettings["view"]["type"] ?? "") === "age") {
	$APPLICATION->IncludeComponent(
		"codematrix:popup.pro",
		"age_default",
		array("ID_POPUP" => $idPopup),
		false
	);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
