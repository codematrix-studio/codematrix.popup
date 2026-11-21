<?php
\Bitrix\Main\Loader::registerAutoLoadClasses(
	"codematrix.popup",
	array(
		"popup" => "classes/general/popup.php",
		"popupEdit" => "classes/general/popup_edit.php",
		"CodeMatrix\\Popup\\Model\\PopupTable" => "lib/model/popup.php",
		"CodeMatrix\\Popup\\Model\\PopupColorTable" => "lib/model/popupcolor.php",
		"CodeMatrix\\Popup\\Model\\PopupTemplateTable" => "lib/model/popuptemplate.php",
		"CodeMatrix\\Popup\\Model\\LegacyRowMapper" => "lib/model/legacyrowmapper.php",
		"CodeMatrix\\Popup\\Mail" => "lib/mail.php",
		"CodeMatrix\\Popup\\FilePath" => "lib/filepath.php",
		"CodeMatrix\\Popup\\PublicRequestGuard" => "lib/publicrequestguard.php"
	)
);
