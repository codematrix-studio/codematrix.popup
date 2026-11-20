<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$idPopup = filter_var(
	$request->get("id_popup"),
	FILTER_VALIDATE_INT,
	array("options" => array("min_range" => 1))
);

// Endpoint закреплён за своим шаблоном: имя компонента нельзя задавать из запроса.
if ($idPopup !== false) {
	$APPLICATION->IncludeComponent(
		"codematrix:popup.pro",
		"age_default",
		array("ID_POPUP" => $idPopup),
		false
	);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
