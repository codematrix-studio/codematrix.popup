<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
$moduleLoaded = \Bitrix\Main\Loader::includeModule("codematrix.popup");
$idPopup = filter_var(
	$request->getPost("id_popup"),
	FILTER_VALIDATE_INT,
	array("options" => array("min_range" => 1))
);

$popupSettings = ($moduleLoaded && $idPopup !== false)
	? (new popup())->getSetting($idPopup)
	: false;

if (
	is_array($popupSettings)
	&& ($popupSettings["view"]["type"] ?? "") === "contact"
	&& ($popupSettings["view"]["template"] ?? "") === "type1"
) {
	$APPLICATION->IncludeComponent(
		"codematrix:popup.pro",
		"contact_type1",
		array("ID_POPUP" => $idPopup),
		false
	);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
