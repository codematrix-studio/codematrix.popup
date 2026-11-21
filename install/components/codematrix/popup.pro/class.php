<?php
use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application,
	Bitrix\Main\Web\Cookie,
	Bitrix\Main\Context,
	Bitrix\Main\UserConsent\Internals\AgreementTable,
	Bitrix\Main\UserConsent\Agreement,
	Bitrix\Main\UserTable;

\Bitrix\Main\Loader::IncludeModule("iblock");
\Bitrix\Main\Loader::IncludeModule("codematrix.popup");

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

class CodeMatrixPopup extends \CBitrixComponent{

		/**
	 * Подготавливает и дополняет параметры компонента попапа.
	 *
	 * @param array $params Параметры компонента.
	 * @return array Подготовленные параметры компонента.
	 */
	public function onPrepareComponentParams($params){
		if(empty($params['ID_POPUP'])){
			$params['ID_POPUP']=1;
		}
		$params['REFERER']=$_SERVER['HTTP_REFERER'] ?? '';
		return $params;
	}

	/**
	 * Формирует результат компонента и подключает выбранный шаблон попапа.
	 *
	 * @return mixed
	 */
	public function executeComponent(){
		global $APPLICATION;
		if(empty($this->arParams["ID_POPUP"])){
			return 'error id popup!';
		}

		if(!empty($this->arParams['MODE']) && $this->arParams['MODE']=='GET_PATH'){
			echo $this->GetPath().'/templates/'.$this->getTemplateName();
			die();
		}
		$this->arResult=array(
			'TITLE'=>'',
			'SUBTITLE'=>'',
			'CONTENT'=>'',
			'LINK_TEXT'=>'',
			'LINK_HREF'=>'http://codematrix.ru',
			'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/gift_1.jpg',
			'IMG_2_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/gift_1.jpg',
			'LINK_VIDEO'=>'0KCGXCyM_K8',
			'VIDEO_SIMILAR'=>'VIDEO_SIMILAR',
			'VIDEO_AUTOPLAY'=>'VIDEO_AUTOPLAY',
			'BUTTON_TEXT_Y'=>'BUTTON_TEXT_Y',
			'BUTTON_TEXT_N'=>'BUTTON_TEXT_N',
			'COLOR_BG'=>'COLOR_BG',
			'ID_VK'=>'ID_VK',
			'ID_FB'=>'ID_FB',
			'ID_INST'=>'ID_INST',
			'ID_ODNKL'=>'ID_ODNKL',
			'TYPE_VIEW'=>'TYPE_VIEW',
			'BUTTON_TEXT'=>'BUTTON_TEXT',
			'VK'=>'VK',
			'FB'=>'FB',
			'TEXTAREA'=>'TEXTAREA',
			'DISCOUNT_MASK'=>'',
			'EMAIL_SHOW'=>'Y',
			'EMAIL_REQUIRED'=>'Y',
			'EMAIL_TITLE'=>'',
			'EMAIL_PLACEHOLDER'=>'',

			'NAME_SHOW'=>'Y',
			'NAME_REQUIRED'=>'Y',
			'NAME_TITLE'=>'',
			'NAME_PLACEHOLDER'=>'',
			
			'LASTNAME_SHOW'=>'Y',
			'LASTNAME_REQUIRED'=>'Y',
			'LASTNAME_TITLE'=>'',
			'LASTNAME_PLACEHOLDER'=>'',

			'PHONE_SHOW'=>'Y',
			'PHONE_REQUIRED'=>'Y',
			'PHONE_TITLE'=>'',
			'PHONE_PLACEHOLDER'=>'',

			'DESCRIPTION_SHOW'=>'Y',
			'DESCRIPTION_REQUIRED'=>'Y',
			'DESCRIPTION_TITLE'=>'',
			'DESCRIPTION_PLACEHOLDER'=>'',
		);

		if(!empty($this->arParams['MODE']) && $this->arParams['MODE']=='TEMPLATE'){
			$this->arResult=array(
				'TITLE'=>'#TITLE#',
				'SUBTITLE'=>'#SUBTITLE#',
				'CONTENT'=>'#CONTENT#',
				'LINK_TEXT'=>'#LINK_TEXT#',
				'LINK_HREF'=>'#LINK_HREF#',
				'IMG_1_SRC'=>'#IMG_1_SRC#',
				'IMG_2_SRC'=>'#IMG_2_SRC#',
				'LINK_VIDEO'=>'#LINK_VIDEO#',
				'VIDEO_SIMILAR'=>'#VIDEO_SIMILAR#',
				'VIDEO_AUTOPLAY'=>'#VIDEO_AUTOPLAY#',
				'COLOR_BG'=>'#COLOR_BG#',
				'ID_VK'=>'#ID_VK#',
				'ID_FB'=>'#ID_FB#',
				'ID_INST'=>'#ID_INST#',
				'ID_ODNKL'=>'#ID_ODNKL#',
				'TYPE_VIEW'=>'#TYPE_VIEW#',
				'BUTTON_TEXT'=>'#BUTTON_TEXT#',
				'COLLECT'=>'#COLLECT#',
				'VK'=>'#VK#',
				'FB'=>'#FB#',
				'TEXTAREA'=>'#TEXTAREA#',
				'EMAIL_SHOW'=>'#EMAIL_SHOW#',
				'EMAIL_REQUIRED'=>'#EMAIL_REQUIRED#',
				'EMAIL_TITLE'=>'#EMAIL_TITLE#',
				'EMAIL_PLACEHOLDER'=>'#EMAIL_PLACEHOLDER#',

				'NAME_SHOW'=>'#NAME_SHOW#',
				'NAME_REQUIRED'=>'#NAME_REQUIRED#',
				'NAME_TITLE'=>'#NAME_TITLE#',
				'NAME_PLACEHOLDER'=>'#NAME_PLACEHOLDER#',
				
				'LASTNAME_SHOW'=>'#LASTNAME_SHOW#',
				'LASTNAME_REQUIRED'=>'#LASTNAME_REQUIRED#',
				'LASTNAME_TITLE'=>'#LASTNAME_TITLE#',
				'LASTNAME_PLACEHOLDER'=>'#LASTNAME_PLACEHOLDER#',

				'PHONE_SHOW'=>'#PHONE_SHOW#',
				'PHONE_REQUIRED'=>'#PHONE_REQUIRED#',
				'PHONE_TITLE'=>'#PHONE_TITLE#',
				'PHONE_PLACEHOLDER'=>'#PHONE_PLACEHOLDER#',

				'USE_CONSENT_SHOW'=>'#USE_CONSENT_SHOW#',
				'CONSENT_LIST'=>'#CONSENT_LIST#',

				'BUTTON_TEXT_Y'=>'#BUTTON_TEXT_Y#',
				'BUTTON_TEXT_N'=>'#BUTTON_TEXT_N#',
				'DISCOUNT_MASK'=>'#DISCOUNT_MASK#',
				'DESCRIPTION_SHOW'=>'#DESCRIPTION_SHOW#',
				'DESCRIPTION_REQUIRED'=>'#DESCRIPTION_REQUIRED#',
				'DESCRIPTION_TITLE'=>'#DESCRIPTION_TITLE#',
				'DESCRIPTION_PLACEHOLDER'=>'#DESCRIPTION_PLACEHOLDER#',
				'SOC_VK'=>'#SOC_VK#',
				'SOC_OD'=>'#SOC_OD#',
				'SOC_FB'=>'#SOC_FB#',
				'SOC_TW'=>'#SOC_TW#',
				'SOC_GP'=>'#SOC_GP#',
				'SOC_MR'=>'#SOC_MR#',
				
				'TIMER'=>'#timer_enable#',
				'TIMER_TEXT'=>'#timer_text#',
				'TIMER_DATE'=>'#timer_date#',
				'TIMER_LEFT'=>'#timer_left#',
				'TIMER_RIGHT'=>'#timer_right#',
				'TIMER_TOP'=>'#timer_top#',
				'TIMER_BOTTOM'=>'#timer_bottom#',
			);
		}else{
			$popup=new popup;
			$tmpRes=$popup->getComponentResult($this->arParams['ID_POPUP']);
			$popupSetting=$popup->getSetting($this->arParams['ID_POPUP']);
			if($popupSetting['view']['type']=='contact'||$popupSetting['view']['type']=='discount'){
				$contactArr=array('NAME'=>'', 'EMAIL'=>'','LASTNAME'=>'', 'PHONE'=>'', 'DESCRIPTION'=>'');
				global $USER;
				if($USER->IsAuthorized()){
					$arUser = UserTable::getById($USER->GetID())->fetch();
					$contactArr['EMAIL']=$arUser['EMAIL'];
					$contactArr['NAME']=$arUser['NAME'];
					$contactArr['LASTNAME']=$arUser['LAST_NAME'];
					$contactArr['PHONE']=$arUser['PERSONAL_PHONE'];
				}
				$context = Application::getInstance()->getContext();
				$request = $context->getRequest();
				$errors=array();
				$cenderData=false;
				$forSendDataArr=array('NAME_FORM'=>$popupSetting['row']['name']);
				foreach($contactArr as $keyContact=>$nextContact){
					if(!empty($tmpRes[$keyContact.'_SHOW']) && $tmpRes[$keyContact.'_SHOW']=='Y'){
						$tmpRes[$keyContact]=$nextContact;
						// Данные формы принимаются только из тела POST-запроса.
						$reqValue = $request->getPost($keyContact);
						if(isset($reqValue)){
							$cenderData=true;
							if (!is_scalar($reqValue)) {
								$errors[$keyContact] = $tmpRes[$keyContact.'_TITLE'] ?: 'Некорректное значение поля.';
								$tmpRes[$keyContact] = '';
								continue;
							}
							$reqValue = (string)$reqValue;
							$tmpRes[$keyContact]=$reqValue;
							if(empty($reqValue) && $tmpRes[$keyContact.'_REQUIRED']=='Y'){
								$errors[$keyContact]=$tmpRes[$keyContact.'_TITLE'];
								$tmpRes[$keyContact]='';
							}else{
								if($keyContact=='EMAIL' && !filter_var($reqValue, FILTER_VALIDATE_EMAIL)){
									$errors[$keyContact]=$tmpRes[$keyContact.'_TITLE'];
								}else{
									if(SITE_CHARSET=='windows-1251'){
										$reqValue=mb_convert_encoding($reqValue, "windows-1251", "utf-8");
									}
									$forSendDataArr[$keyContact]=$reqValue;
								}
							}
						}
					}
				}
				if (
					$cenderData
					&& ($tmpRes['USE_CONSENT_SHOW'] ?? 'N') === 'Y'
					&& !empty($tmpRes['CONSENT_LIST'])
					&& $request->getPost('use_consent') !== 'Y'
				) {
					$errors['CONSENT'] = 'Необходимо подтвердить согласие на обработку персональных данных.';
				}
				if (
					$cenderData
					&& !\CodeMatrix\Popup\PublicRequestGuard::isPopupRendered((int)$this->arParams['ID_POPUP'])
				) {
					$errors['FORM'] = 'Не удалось подтвердить открытие формы. Обновите страницу и попробуйте снова.';
				}
				if(count($errors)>0){
					$tmpRes['ERRORS']=$errors;
				}elseif($cenderData){
					$popupId = (int)$this->arParams['ID_POPUP'];
					$requestFingerprint = hash('sha256', serialize(array(
						$forSendDataArr,
						$request->getPost('use_consent'),
					)));
					$cachedResponse = \CodeMatrix\Popup\PublicRequestGuard::getCachedResponse(
						'contact',
						$popupId,
						$requestFingerprint
					);

					if ($cachedResponse !== null && isset($cachedResponse['response'])) {
						// Точный повтор уже обработанной формы подтверждаем без повторных побочных действий.
						$tmpRes['SUCCESS'] = 'Y';
					}elseif($cachedResponse !== null){
						// Не позволяем заменить данные и повторно выполнить отправку в коротком интервале.
						$tmpRes['ERRORS']['FORM'] = 'Форма уже была отправлена. Повторите попытку позже.';
					}else{
						// Отправить письмо, сохранить данные в инфоблоке и добавить адрес в список рассылки.
						// Сначала фиксируем согласие в журнале Битрикс и только затем обрабатываем данные формы.
						$consentSaved = true;
						if (
							($tmpRes['USE_CONSENT_SHOW'] ?? 'N') === 'Y'
							&& !empty($popupSetting['view']['props']['CONSENT_LIST'])
						) {
							if (!class_exists('Bitrix\\Main\\UserConsent\\Consent')) {
								$consentSaved = false;
							} else {
								$dataArr = array();
								if (!empty($this->arParams['REFERER'])) {
									$dataArr['URL'] = $this->arParams['REFERER'];
								}

								try {
									$consentRecordId = \Bitrix\Main\UserConsent\Consent::addByContext(
										$popupSetting['view']['props']['CONSENT_LIST'],
										'codematrix/popup',
										$popupSetting['row']['id'],
										$dataArr
									);
									if (!$consentRecordId) {
										$consentSaved = false;
									}
								} catch (\Throwable $exception) {
									$consentSaved = false;
								}
							}
						}
						if($consentSaved && !empty($popupSetting['contact'])){

							$postTitle=array('NAME_TITLE', 'EMAIL_TITLE', 'PHONE_TITLE', 'DESCRIPTION_TITLE','LASTNAME_TITLE');
							foreach($postTitle as $nextTitle){
								$forSendDataArr[$nextTitle]='';
								if(!empty($popupSetting['view']['props'][$nextTitle])){
									$forSendDataArr[$nextTitle]=$popupSetting['view']['props'][$nextTitle];
								}
							}

							if(!empty($popupSetting['row']['cont_posttemplate'])){
								\Bitrix\Main\Mail\Event::send([
									'EVENT_NAME' => 'CODEMATRIX_POPUP_SENDER',
									'LID' => SITE_ID,
									'LANGUAGE_ID' => LANGUAGE_ID,
									'C_FIELDS' => $forSendDataArr,
									'DUPLICATE' => 'Y',
									'MESSAGE_ID' => (int)$popupSetting['row']['cont_posttemplate'],
								]);
							}
							if(!empty($popupSetting['contact']['emailList']) && $popupSetting['contact']['emailList']=='Y'){
								$popup->insertToMailList($forSendDataArr['EMAIL'], $forSendDataArr['NAME'], $this->arParams['ID_POPUP']);
							}
							if(!empty($popupSetting['contact']['iblock'])){
								$insertArr=array(
									'IBLOCK_ID'=>$popupSetting['contact']['iblock'],
									'NAME'=>array_shift($forSendDataArr),
									'PREVIEW_TEXT'=>''
								);
								foreach($forSendDataArr as $keyData=>$nextData){
									if(!empty($tmpRes[$keyData.'_TITLE'])){
										$insertArr['PREVIEW_TEXT'].=$tmpRes[$keyData.'_TITLE'].' - '.$nextData.PHP_EOL;
										$propId=CodeMatrix\Popup\Tools::returnPropId($popupSetting['contact']['iblock'], $keyData, $tmpRes[$keyData.'_TITLE']);
										$insertArr['PROPERTY_VALUES'][$propId]=$nextData;
									}
								}
								$el = new CIBlockElement;
								$el->Add($insertArr);
							}
							global $USER;

							if(!empty($popupSetting['contact']['register'])&&$popupSetting['contact']['register']=='Y'&&!empty($forSendDataArr['EMAIL'])&&!$USER->IsAuthorized()){
								$tmpUsers=UserTable::getList(array(
									'select' => array('ID'),
									'filter' => array('=EMAIL' => $forSendDataArr['EMAIL']),
									'order' => array('ID' => 'DESC'),
								));
								$existingUser = $tmpUsers->fetch();
								if (!$existingUser) {
									$registerResult = $USER->SimpleRegister($forSendDataArr['EMAIL']);
									$tmpRes['REG_RES'][] = is_array($registerResult)
										&& $USER->IsAuthorized()
										&& (int)$USER->GetID() > 0;
								} else {
									$tmpRes['REG_RES'][] = false;
								}
							}
						}
						if ($consentSaved) {
							$popup->setStatistic($popupSetting['row']['id'], 1, 'stat_action');
							if (session_status() === PHP_SESSION_ACTIVE) {
								if (!isset($_SESSION['codematrix_popup_statistic_action']) || !is_array($_SESSION['codematrix_popup_statistic_action'])) {
									$_SESSION['codematrix_popup_statistic_action'] = array();
								}
								$_SESSION['codematrix_popup_statistic_action'][$popupId] = true;
							}
							$tmpRes['SUCCESS']='Y';
							\CodeMatrix\Popup\PublicRequestGuard::rememberResponse(
								'contact',
								$popupId,
								'SUCCESS',
								$requestFingerprint
							);
						} else {
							$tmpRes['ERRORS']['CONSENT'] = 'Не удалось сохранить согласие. Данные формы не отправлены.';
						}
					}

					if (!empty($tmpRes['SUCCESS'])) {
						// Установить cookie, чтобы повторно не показывать окно пользователю, отправившему форму.
						$responce = $context->getResponse();
						$cookie = new Cookie("codematrixPopupFilling_".$this->arParams['ID_POPUP'], 'Y', time()+864000000);
						$cookie->setHttpOnly(false);
						$responce->addCookie($cookie);
					}
				}
				elseif($popupSetting['view']['type']=='coupon'){
				
				foreach($popupSetting['view']['props'] as $key=>$prop){
					if($key!='IMG_1_SRC'&&$key!='EMAIL_PLACEHOLDER')
					$tmpRes[$key]=$prop;
				}
			}elseif($popupSetting['view']['type']=='age'){
				$context = Application::getInstance()->getContext();
				$request = $context->getRequest();
				$reqValue = $request->getPost('checked');
				if(isset($reqValue)&&$reqValue=='Y'){
					$responce = $context->getResponse();
					$cookie = new Cookie("codematrixPopupFilling_".$this->arParams['ID_POPUP'], 'Y', time()+864000000);
					$cookie->setHttpOnly(false);
					$responce->addCookie($cookie);
				}
			}
			$this->arResult=$tmpRes;
		}
		$timer_array=array('banner','video','action','contact','html','coupon','roulette','discount');
			if(!empty($popupSetting['view']['type']) && in_array($popupSetting['view']['type'],$timer_array)){
				$timerSettings = is_array($popupSetting['timer'] ?? null) ? $popupSetting['timer'] : array();
				$this->arResult['TIMER'] = $timerSettings['enabled'] ?? 'N';
				$this->arResult['TIMER_TEXT'] = $timerSettings['text'] ?? '';
				if ($this->arResult['TIMER'] === 'Y') {
					$format = 'd.m.Y H:i:s';
					$timerDateValue = $timerSettings['date'] ?? null;
					$timerDate = false;
					if (is_string($timerDateValue)) {
						try {
							$timerDate = \DateTime::createFromFormat($format, $timerDateValue);
						} catch (\Throwable $exception) {
							$timerDate = false;
						}
					}
					$timerDateErrors = \DateTime::getLastErrors();
					$timerDateIsValid = $timerDate instanceof \DateTime
						&& ($timerDateErrors === false || (
							$timerDateErrors['warning_count'] === 0
							&& $timerDateErrors['error_count'] === 0
						));
					if ($timerDateIsValid) {
						$now = new \DateTime();
						$this->arResult['TIMER_DATE'] = $now->diff($timerDate)->format('%a:%H:%I:%S');
						$this->arResult['TIMER_LEFT'] = $timerSettings['left'] ?? '';
						$this->arResult['TIMER_RIGHT'] = $timerSettings['right'] ?? '';
						$this->arResult['TIMER_TOP'] = $timerSettings['top'] ?? '';
						$this->arResult['TIMER_BOTTOM'] = $timerSettings['bottom'] ?? '';
					} else {
						// Не показываем таймер с повреждённой датой в старой записи.
						$this->arResult['TIMER'] = 'N';
					}
				}
			}
		if(!empty($popupSetting['view']['type']) && $popupSetting['view']['type']=='roulette'){
			$this->arResult['ELEMENTS']=$popupSetting['roulett'];
			unset($this->arResult['ELEMENTS']['count']);
			$this->arResult['ELEMENTS_COUNT']=$popupSetting['roulett']['count'];
		}
		// Подготовка данных согласия на обработку персональных данных.
		if((!empty($popupSetting['view']['type']) && ($popupSetting['view']['type']=='contact'||$popupSetting['view']['type']=='discount')) || $this->arParams['MODE']=='TEMPLATE'){
			if(!empty($popup)){
				$agreements=$popup->getAgreements(array('button_caption'=>$popupSetting['view']['props']['BUTTON_TEXT']));
				$this->arResult['AGREEMENTS']=$agreements;
				if(!empty($popupSetting['view']['props']['CONSENT_LIST'])){
					$this->arResult['CONSENT_LIST']=$agreements[$popupSetting['view']['props']['CONSENT_LIST']];
					$this->arResult['CONSENT_ID']=$popupSetting['view']['props']['CONSENT_LIST'];
				}
			}else{
				$popup=new popup;
				$agreements=$popup->getAgreements(array('button_caption'=>'#BUTTON_TEXT#'));
				$this->arResult['AGREEMENTS']=$agreements;
				$this->arResult['CONSENT_ID']=1;
			}
		}
		// Завершение подготовки данных согласия.
		
		$this->IncludeComponentTemplate();
	}
}
}
