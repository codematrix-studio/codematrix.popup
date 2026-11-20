<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main,
	Bitrix\Main\Application,
	Bitrix\Main\Page\Asset,
	Bitrix\Main\Request,
	Bitrix\Main\Localization\Loc;
	
	Loc::loadMessages(__FILE__);
	
if(\Bitrix\Main\Loader::includeModule('codematrix.popup')){
	// Получаем параметры AJAX-запроса.
	$context = Application::getInstance()->getContext();
	$request = $context->getRequest();
	$popupsO=new popup;
	$reqArr=array(
		'type'=>$request->get('type'),
		'popupIds'=>$request->get('popupIds'),
		'popupId'=>$request->get('popupId'),
		'popupTime'=>$request->get('popupTime'),
	);
	$pageUrl = (string)($_SERVER['HTTP_REFERER'] ?? '');
	if ($pageUrl === '') {
		$requestedPageUrl = $request->get('pageUrl');
		if (is_string($requestedPageUrl) && $requestedPageUrl !== '') {
			$requestedPageUrl = urldecode($requestedPageUrl);
			$requestedHost = parse_url($requestedPageUrl, PHP_URL_HOST);
			$currentHost = parse_url('http://' . (string)($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
			if ($requestedHost === null || ($currentHost !== null && strcasecmp($requestedHost, $currentHost) === 0)) {
				$pageUrl = $requestedPageUrl;
			}
		}
	}
	if ($pageUrl === '') {
		$pageUrl = '/';
	}
	$getAvailablePopups = static function () use ($popupsO, $pageUrl) {
		return $popupsO->getAvailablePopups(array(
			'site' => defined('SITE_ID') ? SITE_ID : '',
			'dateUser' => time(),
			'pageUrl' => $pageUrl,
			'countPages' => max(1, (int)($_SESSION['codematrix_popup_count_pages'] ?? 1)),
		));
	};
	if(!empty($reqArr['type'])){
		if($reqArr['type']=='getPopups'){
			if(empty($_SESSION['codematrix_popup_count_pages'])){
				$_SESSION['codematrix_popup_count_pages']=1;
			}else{
				$_SESSION['codematrix_popup_count_pages']=$_SESSION['codematrix_popup_count_pages']+1;
			}
			$retStr=$getAvailablePopups();
			$retStr=CUtil::PhpToJSObject($retStr);
			echo str_replace("'", '"', $retStr);
		}elseif($reqArr['type']=='getBasket'){
			$basket=popup::GetBasketInfo();
			$retStr=CUtil::PhpToJSObject($basket);
			echo str_replace("'", '"', $retStr);
		}elseif($reqArr['type']=='getTemplatePath' && is_array($reqArr['popupIds'])){
			$availablePopups=$getAvailablePopups();
			$popupIds=array();
			foreach($reqArr['popupIds'] as $popupId){
				$popupId=filter_var($popupId, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
				if($popupId!==false && isset($availablePopups[$popupId])){
					$popupIds[]=$popupId;
				}
			}
			$paths=count($popupIds)>0 ? $popupsO->getComponentPath($popupIds) : array();
			$retStr=CUtil::PhpToJSObject($paths);
			echo str_replace("'", '"', $retStr);
		}elseif($reqArr['type']=='getHTML'){
			$popupId=filter_var($reqArr['popupId'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
			$availablePopups=$getAvailablePopups();
			$hasFormData=false;
			foreach(array('NAME','EMAIL','LASTNAME','PHONE','DESCRIPTION','checked') as $fieldName){
				if($request->get($fieldName)!==null){
					$hasFormData=true;
					break;
				}
			}
			if($popupId!==false && isset($availablePopups[$popupId]) && !$hasFormData){
				$popupsO->getHTMLByPopup($popupId);
			}
		}elseif($reqArr['type']=='statisticShow' || $reqArr['type']=='statisticAction'){
			$popupId=filter_var($reqArr['popupId'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
			if($popupId!==false){
				$field=($reqArr['type']=='statisticShow') ? 'stat_show' : 'stat_action';
				$retStr=$popupsO->setStatistic($popupId, 1, $field);
				$retStr=CUtil::PhpToJSObject($retStr);
				echo str_replace("'", '"', $retStr);
			}
		}elseif($reqArr['type']=='statisticTime'){
			$popupId=filter_var($reqArr['popupId'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
			$popupTime=filter_var($reqArr['popupTime'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
			if($popupId!==false && $popupTime!==false){
				$retStr=$popupsO->setStatistic($popupId, $popupTime, 'stat_time');
				$retStr=CUtil::PhpToJSObject($retStr);
				echo str_replace("'", '"', $retStr);
			}
		}
	}
}else{
	echo 'module codematrix.popup not included!';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>