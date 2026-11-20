<?php
use Bitrix\Main;

require_once dirname(__DIR__).'/lib/mail.php';
IncludeModuleLangFile(__FILE__);
Class codematrix_popup extends CModule
{
	const MODULE_ID = 'codematrix.popup';
	var $MODULE_ID = 'codematrix.popup';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	/**
	 * Инициализирует метаданные модуля из файла версии и языковых сообщений.
	 *
	 * @return void
	 */
	function __construct(){
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("codematrix.popup_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("codematrix.popup_MODULE_DESCRIPTION");

		$this->PARTNER_NAME = GetMessage("codematrix.popup_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("codematrix.popup_PARTNER_URI");
	}

	/**
	 * Создаёт таблицы модуля, если они ещё не существуют.
	 *
	 * @param array $arParams Дополнительные параметры установки.
	 * @return bool Результат выполнения этапа установки БД.
	 */
	function InstallDB($arParams = array()){
		global $DB;
		$DB->Query('CREATE TABLE IF NOT EXISTS codematrix_popup (
			id INT NOT NULL AUTO_INCREMENT,
			sort INT NOT NULL,
			active VARCHAR(1) DEFAULT NULL,
			name VARCHAR(50) DEFAULT NULL,
			type VARCHAR(30) NOT NULL,
			stat_show INT DEFAULT NULL,
			stat_time INT DEFAULT NULL,
			stat_action INT DEFAULT NULL,
			settings TEXT NOT NULL,
			cont_iblock INT DEFAULT NULL,
			cont_posttemplate INT DEFAULT NULL,
			PRIMARY KEY (id)
		);');
		$DB->Query('CREATE TABLE IF NOT EXISTS codematrix_popup_add_colors (
			id INT NOT NULL AUTO_INCREMENT,
			template VARCHAR(50) NOT NULL,
			name VARCHAR(50) DEFAULT NULL,
			PRIMARY KEY (id));'
		);
		$DB->Query('CREATE TABLE IF NOT EXISTS codematrix_popup_add_templates(
			id INT NOT NULL AUTO_INCREMENT,
			name VARCHAR(50) NOT NULL,
			type VARCHAR(50) NOT NULL,
			template TEXT NOT NULL,
			PRIMARY KEY (id));'
		);
		//RegisterModuleDependences("main","OnEpilog", $this->MODULE_ID, "popup","createPopup");
		//RegisterModuleDependences("main","OnEndBufferContent", $this->MODULE_ID, "popup","setBufferContent");
		return true;
	}

	/**
	 * Удаляет таблицы модуля при полном удалении его данных.
	 *
	 * @param array $arParams Дополнительные параметры удаления.
	 * @return bool Результат выполнения этапа удаления БД.
	 */
	function UnInstallDB($arParams = array()){
		global $DB;
		$DB->Query('DROP TABLE IF EXISTS codematrix_popup;');
		$DB->Query('DROP TABLE IF EXISTS codematrix_popup_add_colors;');
		$DB->Query('DROP TABLE IF EXISTS codematrix_popup_add_templates;');
		//UnRegisterModuleDependences("main","OnEpilog", $this->MODULE_ID, "popup","createPopup");
		//UnRegisterModuleDependences("main","OnEndBufferContent", $this->MODULE_ID, "popup","setBufferContent");
		return true;
	}

	/**
	 * Регистрирует обработчики событий модуля.
	 *
	 * @return bool Результат регистрации обработчиков.
	 */
	function InstallEvents(){
		RegisterModuleDependences("main","OnBeforeEndBufferContent", $this->MODULE_ID, "popup", "insertPopups");
		return true;
	}

	/**
	 * Удаляет зарегистрированные обработчики событий модуля.
	 *
	 * @return bool Результат удаления обработчиков.
	 */
	function UnInstallEvents(){
		UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", $this->MODULE_ID, "popup", "insertPopups");
		return true;
	}
	/**
	 * Создаёт типы почтовых событий, используемые модулем.
	 *
	 * @return void
	 */
	function InstallPostEvent(){
		\CodeMatrix\Popup\Mail::ensureEventType(
			'CODEMATRIX_POPUP_SEND_COUPON',
			'ru',
			GetMessage("codematrix.popup_EVENT_NAME"),
			str_ireplace("\t", '',GetMessage("codematrix.popup_EVENT_DESCRIPTION"))
		);
		\CodeMatrix\Popup\Mail::ensureEventType(
			'CODEMATRIX_POPUP_ROULETTE_SEND',
			'ru',
			GetMessage("codematrix.popup_EVENT_NAME_ROULETTE"),
			str_ireplace("\t", '',GetMessage("codematrix.popup_EVENT_DESCRIPTION_ROULETTE"))
		);
	}

	/**
	 * Удаляет типы почтовых событий модуля.
	 *
	 * @return void
	 */
	function UnInstallPostEvent(){
		\CodeMatrix\Popup\Mail::deleteEventTypes('CODEMATRIX_POPUP_SEND_COUPON');
		\CodeMatrix\Popup\Mail::deleteEventTypes('CODEMATRIX_POPUP_ROULETTE_SEND');
	}

	/**
	 * Создаёт стандартные почтовые шаблоны для всех сайтов.
	 *
	 * @return void
	 */
	function InstallPostTemplate(){
		$sites=Bitrix\Main\SiteTable::getList(['select' => ['LID']]);
		$lids=array();
		while($site=$sites->Fetch()){
			$lids[]=$site['LID'];
		}

		$template=array(
			'LID'=>$lids,
			'EVENT_NAME'=>'CODEMATRIX_POPUP_SEND_COUPON',
			'ACTIVE'=>'Y',
			'EMAIL_FROM'=>'#DEFAULT_EMAIL_FROM#',
			'EMAIL_TO'=>'#EMAIL#',
			'SUBJECT'=>GetMessage("codematrix.popup_TEMPLATE_SUBJECT"),
			'BODY_TYPE'=>'html',
			'MESSAGE'=>str_ireplace("\t", '',GetMessage("codematrix.popup_TEMPLATE_MESSAGE"))
		);
		\CodeMatrix\Popup\Mail::addTemplate($template);

		$template['EVENT_NAME']='CODEMATRIX_POPUP_ROULETTE_SEND';
		$template['SUBJECT']=GetMessage("codematrix.popup_TEMPLATE_SUBJECT_ROULETTE");
		$template['MESSAGE']=str_ireplace("\t", '',GetMessage("codematrix.popup_TEMPLATE_MESSAGE_ROULETTE"));
		\CodeMatrix\Popup\Mail::addTemplate($template);
	}

	/**
	 * Удаляет почтовые шаблоны, созданные модулем.
	 *
	 * @return void
	 */
	function UnInstallPostTemplate(){
		\CodeMatrix\Popup\Mail::deleteTemplates('CODEMATRIX_POPUP_SEND_COUPON');
		\CodeMatrix\Popup\Mail::deleteTemplates('CODEMATRIX_POPUP_ROULETTE_SEND');
	}

	/**
	 * Копирует административные файлы, компоненты и ресурсы модуля в ядро сайта.
	 *
	 * @return bool Результат выполнения этапа установки файлов.
	 */
	function InstallFiles(){
		
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components')){
			if ($dir = opendir($p)){
				while (false !== $item = readdir($dir)){
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
		
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		return true;
	}

	/**
	 * Удаляет установленные модулем компоненты и публичные ресурсы.
	 *
	 * @return bool Результат выполнения этапа удаления файлов.
	 */
	function UnInstallFiles(){
		
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components')){
			if ($dir = opendir($p)){
				while (false !== $item = readdir($dir)){
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0)){
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		
		DeleteDirFilesEx("/upload/".$this->MODULE_ID);
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		DeleteDirFilesEx("/bitrix/themes/.default/icons/".$this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID);
		return true;
	}

	/**
	 * Выполняет полный цикл установки модуля.
	 *
	 * @return void
	 */
	function DoInstall(){
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		$this->InstallEvents();
		$this->InstallPostEvent();
		$this->InstallPostTemplate();
	}

	/**
	 * Выполняет полный цикл удаления модуля.
	 *
	 * @return void
	 */
	function DoUninstall(){
		global $APPLICATION;
		$this->UnInstallEvents();
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallPostTemplate();
		$this->UnInstallPostEvent();
	}
}
?>
