<?php
use \Bitrix\Main\Application,
	Bitrix\Main,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\UserConsent\Internals\AgreementTable,
	Bitrix\Main\UserConsent\Agreement;
	
Loc::loadMessages(__FILE__);
class popupEdit extends popup{
	
	/**
	 * Возвращает ID почтового шаблона контактной формы, при необходимости создавая тип события и шаблон.
	 *
	 * @return int|string|false ID почтового шаблона.
	 */
	private function getPostTemplate(){
		$tmpSettings=$this->getSetting($this->idPopup);
		\CodeMatrix\Popup\Mail::ensureEventType(
			'CODEMATRIX_POPUP_SENDER',
			LANGUAGE_ID,
			GetMessage("codematrix.popup_EVENT_TYPE_NAME"),
			'#EMAIL# - mail
#NAME# - name
#PHONE# - phone
#DESCRIPTION# - description
#EMAIL_TITLE# - email_title
#NAME_TITLE# - name_title
#PHONE_TITLE# - phone_title
#DESCRIPTION_TITLE# - description_title'
		);

		if(empty($tmpSettings['row']['cont_posttemplate'])){
			$lids=array();
			$rsSites = \Bitrix\Main\SiteTable::getList(['select' => ['LID'], 'order' => ['SORT' => 'DESC']]);
			while ($arSite = $rsSites->Fetch()){
				$lids[]=$arSite['LID'];
			}

			$tmpSettings['row']['cont_posttemplate']=\CodeMatrix\Popup\Mail::addTemplate(array(
				'EVENT_NAME'=>'CODEMATRIX_POPUP_SENDER',
				'ACTIVE'=>'Y',
				'LID'=>$lids,
				'EMAIL_FROM'=>'#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'=>'#DEFAULT_EMAIL_FROM#',
				'SUBJECT'=>GetMessage("codematrix.popup_POST_SEND").' #NAME_FORM#',
				'BODY_TYPE'=>'text',
				'MESSAGE'=>'
#EMAIL_TITLE# - #EMAIL#
#NAME_TITLE# - #NAME# 
#PHONE_TITLE# - #PHONE#
#DESCRIPTION_TITLE# - #DESCRIPTION#
'
			));
		}
		return $tmpSettings['row']['cont_posttemplate'];
	}

	/**
	 * Сохраняет настройки попапа из POST-запроса, включая параметры его почтовых шаблонов.
	 *
	 * @param \Bitrix\Main\HttpRequest $request HTTP-запрос с параметрами формы.
	 * @return mixed Результат сохранения.
	 */
	public function editPopup($request){		
		$strError = '';
		$settings=array('view'=>array(), 'condition'=>array());

		$type = (string)$request->getPost('type');
		$templateName = (string)$request->getPost('template');
		$availableTemplates = $this->getTemplates();

		if (!isset($availableTemplates[$type]) || !is_array($availableTemplates[$type])) {
			return array('status' => 'error', 'data' => 'Выбран неизвестный тип попапа.');
		}

		$selectedTemplate = null;
		foreach ($availableTemplates[$type] as $templateItem) {
			if (($templateItem['template'] ?? null) === $templateName) {
				$selectedTemplate = $templateItem;
				break;
			}
		}

		if (
			!is_array($selectedTemplate)
			|| !isset($selectedTemplate['props'])
			|| !is_array($selectedTemplate['props'])
		) {
			return array('status' => 'error', 'data' => 'Выбранный шаблон не относится к указанному типу попапа.');
		}

		$settings['view']['type'] = $type;
		$settings['view']['color_style'] = $request->getPost('color_style');
		$settings['view']['template'] = $templateName;
		$viewStructure = $selectedTemplate['props'];
		foreach($viewStructure as $keyNextViewEl=>$nextViewEl){
			$tmpPost=$request->getPost($keyNextViewEl);
			if($keyNextViewEl=='EMAIL_TEMPLATE'){
				if($request->getPost('id_popup')=='new'){
					$template['LID']=array();
					$sites=\Bitrix\Main\SiteTable::getList(['select' => ['LID']]);
					while($s=$sites->Fetch())
						$template['LID'][]=$s['LID'];
					$template['EVENT_NAME']="CODEMATRIX_POPUP_SEND_COUPON";
					$template['ACTIVE']='Y';
					$template['EMAIL_FROM']='#DEFAULT_EMAIL_FROM#';
					$template['EMAIL_TO']='#EMAIL#';
					$template['SUBJECT']=GetMessage("codematrix.popup_TEMPLATE_SUBJECT");
					$template['BODY_TYPE']='html';
					$template['MESSAGE']=GetMessage("codematrix.popup_TEMPLATE_MESSAGE");
					$tmpPost=\CodeMatrix\Popup\Mail::addTemplate($template);
				}
			}
			if($keyNextViewEl=='MAIL_TEMPLATE'){
				if($request->getPost('id_popup')=='new'){					
					$keyPost='CODEMATRIX_POPUP_ROULETTE_SEND';
					\CodeMatrix\Popup\Mail::ensureEventType(
						$keyPost,
						'ru',
						GetMessage("codematrix.popup_EVENT_NAME_ROULETTE"),
						str_ireplace("\t", '',GetMessage("codematrix.popup_EVENT_DESCRIPTION_ROULETTE"))
					);
					$template['LID']=array();
					$sites=\Bitrix\Main\SiteTable::getList(['select' => ['LID']]);
					while($s=$sites->Fetch())
						$template['LID'][]=$s['LID'];
					$template['EVENT_NAME']=$keyPost;
					$template['ACTIVE']='Y';
					$template['EMAIL_FROM']='#DEFAULT_EMAIL_FROM#';
					$template['EMAIL_TO']='#EMAIL#';
					$template['SUBJECT']=GetMessage("codematrix.popup_TEMPLATE_SUBJECT_ROULETTE");
					$template['BODY_TYPE']='html';
					$template['MESSAGE']=GetMessage("codematrix.popup_TEMPLATE_MESSAGE_ROULETTE");
					$tmpPost=\CodeMatrix\Popup\Mail::addTemplate($template);
				}
			}
			if($keyNextViewEl=='EMAIL_TEMPLATE_D'){
				if($request->getPost('id_popup')=='new'){					
					$keyPost='CODEMATRIX_POPUP_DISCOUNT_SEND';
					\CodeMatrix\Popup\Mail::ensureEventType(
						$keyPost,
						'ru',
						GetMessage("codematrix.popup_EVENT_NAME_DISCOUNT"),
						str_ireplace("\t", '',GetMessage("codematrix.popup_EVENT_DESCRIPTION_DISCOUNT"))
					);
					$template['LID']=array();
					$sites=\Bitrix\Main\SiteTable::getList(['select' => ['LID']]);
					while($s=$sites->Fetch())
						$template['LID'][]=$s['LID'];
					$template['EVENT_NAME']=$keyPost;
					$template['ACTIVE']='Y';
					$template['EMAIL_FROM']='#DEFAULT_EMAIL_FROM#';
					$template['EMAIL_TO']='#EMAIL#';
					$template['SUBJECT']=GetMessage("codematrix.popup_TEMPLATE_SUBJECT_DISCOUNT");
					$template['BODY_TYPE']='html';
					$template['MESSAGE']=GetMessage("codematrix.popup_TEMPLATE_MESSAGE_DISCOUNT");
					$tmpPost=\CodeMatrix\Popup\Mail::addTemplate($template);
				}
			}
			$currentVal=(!isset($tmpPost))?$nextViewEl:$tmpPost;
			$settings['view']['props'][$keyNextViewEl]=$currentVal;
		}

		$conditionSet=$this->getConditions();
		foreach($conditionSet as $keyNextCond=>$nextCond){
			if($keyNextCond=='timeInterval'){
				$settings['condition']['timeInterval']='';
				$tmpPeriodFrom=$request->getPost('period_from');
				$tmpPeriodTo=$request->getPost('period_to');
				if(!empty($tmpPeriodFrom) || !empty($tmpPeriodTo)){
					$settings['condition']['timeInterval']=$tmpPeriodFrom.'#'.$tmpPeriodTo;
				}
			}elseif($keyNextCond=='dateStart' || $keyNextCond=='dateFinish'){
				// Получаем формат именно текущего сайта в формате, ожидаемом MakeTimeStamp().
				$dateTimeFormat = \CSite::GetDateFormat('FULL', SITE_ID);

				$settings['condition'][$keyNextCond] = MakeTimeStamp(
					$request->getPost($keyNextCond),
					$dateTimeFormat
				);
			}elseif($keyNextCond=='saleIDProdInBasket'){
				$tmpProds=array();
				foreach($request->getPost('saleIDProdInBasket') as $nextProd){
					if(!empty($nextProd)){
						$tmpProds[]=$nextProd;
					}
				}
				$settings['condition'][$keyNextCond]=(count($tmpProds)==0)?'':$tmpProds;
			}else{
				$settings['condition'][$keyNextCond]=$request->getPost($keyNextCond);
			}
		}
		
		// Настройки формы сбора контактных данных.
		if($settings['view']['type']=='contact'){
			$contactSendMail=$request->getPost("contact_send_mail");
			$contactSaveToIblock=$request->getPost("contact_save_to_iblock");
			$contactIblock=$request->getPost("contact_iblock");
			$contactSaveToList=$request->getPost("contact_save_to_list");
			$contactRegister=$request->getPost("contact_register");
			if(!empty($contactSendMail) || (!empty($contactSaveToIblock) && !empty($contactIblock)) || !empty($contactSaveToList) || !empty($contactRegister)){
				if(!empty($contactSaveToList)){
					$settings['contact']['emailList']='Y';
				}
				if(!empty($contactSaveToIblock) && !empty($contactIblock)){
					$settings['contact']['iblock']=$contactIblock;
				}
				if(!empty($contactSendMail)){
					$settings['contact']['posttemplate']=$this->getPostTemplate();
				}
				if(!empty($contactRegister)){
					$settings['contact']['register']=$contactRegister;
				}
			}
		}
		$timer_array=array('banner','video','action','contact','html','coupon','roulette','discount');
		if(in_array($settings['view']['type'],$timer_array)){
			$settings['timer']=array();
			$timerEnabled=$request->getPost("timer_enable");
			$timerTime=$request->getPost("timer_date");
			$timerText=$request->getPost("timer_text");
			$timerLeft=$request->getPost("timer_left");
			$timerRight=$request->getPost("timer_right");
			$timerTop=$request->getPost("timer_top");
			$timerBottom=$request->getPost("timer_bottom");
			$settings['timer']['enabled']=$timerEnabled;
			$settings['timer']['date']=$timerTime;
			$settings['timer']['text']=$timerText;
			$settings['timer']['left']=$timerLeft;
			$settings['timer']['right']=$timerRight;
			$settings['timer']['top']=$timerTop;
			$settings['timer']['bottom']=$timerBottom;
		}
		if($settings['view']['type']=='roulette'){
			$countRow = $request->getPost("roulette_element_count");
			for($i=1;$i<=$countRow;$i++){
				$settings['roulett'][$i]['text']=$request->getPost("roulette_".$i."_text");
				$settings['roulett'][$i]['color']=$request->getPost("roulette_".$i."_color");
				$settings['roulett'][$i]['rule']=$request->getPost("roulette_".$i."_rule");
			}
			$settings['roulett']['count']=$countRow;
		}
		$serviceName=$request->getPost("service_name");
		$serviceName=(!empty($serviceName))?$serviceName:GetMessage("codematrix.popup_TABCOND_SERVICE_NAME").'_'.$this->idPopup;
		$arFieldsInsert=array(
			'SORT'=>$request->getPost("sort"),
			'ACTIVE'=>$request->getPost("active"),
			'NAME'=>$serviceName,
			'TYPE'=>$request->getPost("type"),
			'SETTINGS'=>serialize($settings)
		);
		if(!empty($settings['contact']['posttemplate'])){
			$arFieldsInsert['CONT_POSTTEMPLATE']=$settings['contact']['posttemplate'];
		}
		if(!empty($settings['contact']['iblock'])){
			$arFieldsInsert['CONT_IBLOCK']=$settings['contact']['iblock'];
		}
		if($request->getPost("id_popup")=='new'){
			$saveResult = \CodeMatrix\Popup\Model\PopupTable::add($arFieldsInsert);
			$ID = $saveResult->getId();
		}else{
			\CodeMatrix\Popup\Model\PopupTable::update($request->getPost("id_popup"), $arFieldsInsert);
			$ID = $request->getPost("id_popup");
		}
		if(strlen($strError)>0){
			return array('status'=>'error', 'data'=>$strError);
		}
		return array('status'=>'success', 'data'=>$ID);
	}
	
	/**
	 * Обновляет активность, сортировку и название попапа из административного списка.
	 *
	 * @param int|string $id Идентификатор попапа.
	 * @param array $arFields Изменяемые поля записи.
	 * @return void
	 */
	public function editFromTableList($id, $arFields){
		$res = \CodeMatrix\Popup\Model\PopupTable::getList(array(
			'filter'=>array('=ID'=>$id),
			'select'=>array('*'),
			'limit'=>1
		));
		$ormRow = $res->fetch();
		$row = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);
		if($row){
			$tmpSetting = \CodeMatrix\Popup\Model\LegacyRowMapper::unserializeArray($row['settings']);
			if (empty($tmpSetting)) {
				return;
			}
			$updFields=array();
			$updFields['ACTIVE']='N';
			$tmpSetting['condition']['active']='N';
			if(!empty($arFields['active'])){
				$updFields['ACTIVE']=$arFields['active'];
				$tmpSetting['condition']['active']=$arFields['active'];
			}
			if(!empty($arFields['SORT'])){
				$updFields['SORT']=intval($arFields['SORT']);
				$tmpSetting['condition']['sort']=intval($arFields['SORT']);
			}
			if(!empty($arFields['NAME'])){
				$updFields['NAME']=$arFields['NAME'];
				$tmpSetting['condition']['service_name']=$arFields['NAME'];
			}
			$updFields['SETTINGS']=serialize($tmpSetting);
			\CodeMatrix\Popup\Model\PopupTable::update($id, $updFields);
		}
	}
	
	/**
	 * Возвращает настройки всех сохранённых попапов, сгруппированные по ID.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getAllPopups(){
		$res = \CodeMatrix\Popup\Model\PopupTable::getList(array(
			'select'=>array('ID', 'SETTINGS'),
			'order'=>array('ID'=>'ASC')
		));
		$retArr=array();
		while($ormRow = $res->fetch()){
			$row = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);
			$retArr[$row['id']] = \CodeMatrix\Popup\Model\LegacyRowMapper::unserializeArray($row['settings']);
		}
		return $retArr;
	}
	
	/**
	 * Преобразует значение элемента массива из кодировки языка сайта в UTF-8.
	 * Используется при рекурсивной обработке пользовательского шаблона.
	 *
	 * @param mixed $item Значение элемента, передаваемое по ссылке.
	 * @param int|string $key Ключ элемента массива.
	 * @return void
	 */
	public function convertToUtf(&$item, $key){
		$item=iconv(LANG_CHARSET, "UTF-8", $item);
	}
	
	/**
	 * Создаёт пользовательскую копию файлового шаблона и сохраняет её настройки.
	 *
	 * @param string $type Тип попапа.
	 * @param string $template Код исходного шаблона.
	 * @param string $newname Название пользовательского шаблона.
	 * @return array|false|null Данные созданного шаблона, false при отсутствии исходного каталога.
	 */
	public function setTemplate($type, $template, $newname){
		if(LANG_CHARSET=='windows-1251'){
			$newname=iconv("UTF-8", LANG_CHARSET, $newname);
		}
		$oldPath='/bitrix/components/codematrix/popup.pro/templates/'.$type.'_'.$template;
		$newPath='/bitrix/components/codematrix/popup.pro/templates/'.$type.'_custom_';
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$oldPath)){
			$templates=$this->getTemplatesPreset();
			foreach($templates[$type] as $nextTemplate){
				if($nextTemplate['template']==$template){
					$copyTemplate=$nextTemplate;
					$copyTemplate['name']=$newname;
					$copyTemplate['active']=false;
					$saveResult = \CodeMatrix\Popup\Model\PopupTemplateTable::add(array(
						'TYPE'=>$type,
						'NAME'=>$newname,
						'TEMPLATE'=>'start'
					));
					$ID = $saveResult->getId();
					$copyTemplate['template']='custom_'.$ID;
					\CodeMatrix\Popup\Model\PopupTemplateTable::update($ID, array(
						'TEMPLATE'=>serialize($copyTemplate)
					));
					CopyDirFiles($_SERVER["DOCUMENT_ROOT"].$oldPath, $_SERVER["DOCUMENT_ROOT"].$newPath.$ID, true, true, false);
					if(LANG_CHARSET=='windows-1251'){
						array_walk_recursive($copyTemplate, array($this, 'convertToUtf'));
					}
					return array(
						'popup'=>$copyTemplate,
						'newPath'=>$newPath.$ID.'/template.php',
						'code'=>"custom_".$ID,
						'id'=>$ID
					);
				}
			}
		}else{
			return false;
		}
	}
	
	/**
	 * Создаёт пользовательскую копию CSS-файла цветовой темы.
	 *
	 * @param string $type Тип попапа.
	 * @param string $template Код шаблона.
	 * @param string $colorstyle Код исходной цветовой темы.
	 * @param string $newname Название пользовательской темы.
	 * @return array Результат операции и данные созданной темы.
	 */
	public function setColorTheme($type, $template, $colorstyle, $newname){
		$retArr=array('status'=>'error update');
		if(LANG_CHARSET=='windows-1251'){
			$newname=iconv("UTF-8", LANG_CHARSET, $newname);
		}
		$oldPath='/bitrix/components/codematrix/popup.pro/templates/'.$type.'_'.$template.'/themes/'.$colorstyle.'.css';
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$oldPath)){
				$saveResult = \CodeMatrix\Popup\Model\PopupColorTable::add(array(
				'TEMPLATE'=>$type.'_'.$template,
				'NAME'=>$newname
			));
			$ID = $saveResult->getId();
			$newPath=str_replace($colorstyle, 'custom_'.$ID, $oldPath);
			copy($_SERVER["DOCUMENT_ROOT"].$oldPath , $_SERVER["DOCUMENT_ROOT"].$newPath);

			$retArr['status']='success';
			$retArr['newPath']=$newPath;
			$retArr['code']='custom_'.$ID;
			$retArr['id']=$ID;
		}
		/*$retArr=array(
			'code'=>"custom_1",
			'newPath'=>"/bitrix/components/codematrix/popup.pro/templates/contact_type4/themes/custom_1.css",
			'status'=>"success",
			'id'=>2
		);*/
		return $retArr;
	}
	
	
	/**
	 * Создаёт копию записи попапа с сохранением её настроек и связанных полей.
	 *
	 * @param int|string $id Идентификатор исходного попапа.
	 * @return int|string|false ID новой записи либо false, если исходная запись не найдена.
	 */
	public function CopyPopup($id){
		$res = \CodeMatrix\Popup\Model\PopupTable::getList(array(
			'filter'=>array('=ID'=>$id),
			'select'=>array('SORT', 'ACTIVE', 'NAME', 'TYPE', 'SETTINGS', 'CONT_IBLOCK', 'CONT_POSTTEMPLATE')
		));
		$tmpFields=array();
		$ormRow = $res->fetch();
		$r = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);
		if($r){
			$tmpFields=array_change_key_case($r, CASE_UPPER);
			$saveResult = \CodeMatrix\Popup\Model\PopupTable::add($tmpFields);
			return $saveResult->getId();
		}else
			return false;
	}
}

?>