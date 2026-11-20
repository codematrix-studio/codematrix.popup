<?php
\Bitrix\Main\Loader::registerAutoLoadClasses(
	"codematrix.popup",
	array(
		"popup" => "classes/general/popup.php",
		"popupEdit" => "classes/general/popup_edit.php",
		"CodeMatrix\\Popup\\Mail" => "lib/mail.php",
		"CodeMatrix\\Popup\\FilePath" => "lib/filepath.php"
	)
);
