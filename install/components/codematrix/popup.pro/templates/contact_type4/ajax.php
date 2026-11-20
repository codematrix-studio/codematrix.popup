<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
$idPopup = filter_var(
	$request->getPost("id_popup"),
	FILTER_VALIDATE_INT,
	array("options" => array("min_range" => 1))
);

if ($idPopup !== false) {
	$APPLICATION->IncludeComponent(
		"codematrix:popup.pro",
		"contact_type4",
		array("ID_POPUP" => $idPopup),
		false
	);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
