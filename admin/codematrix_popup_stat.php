<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main\Application,
	Bitrix\Main\Page\Asset,
	Bitrix\Main\Request,
	Bitrix\Sale\Internals,
	Bitrix\Main\Localization\Loc;
	
Loc::loadMessages(__FILE__);

$module_id="codematrix.popup";

// Подключение административной части страницы статистики.
\Bitrix\Main\Loader::IncludeModule($module_id);
$APPLICATION->SetTitle(GetMessage("codematrix.popup_STAT_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

echo GetMessage("codematrix.popup_STAT_TITLE");
?>

CodeMatrix Popup STAT
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>