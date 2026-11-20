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
	// Сессионные маркеры статистики должны иметь предсказуемый тип.
	if (!isset($_SESSION['codematrix_popup_rendered']) || !is_array($_SESSION['codematrix_popup_rendered'])) {
		$_SESSION['codematrix_popup_rendered'] = array();
	}
	if (!isset($_SESSION['codematrix_popup_statistic_started']) || !is_array($_SESSION['codematrix_popup_statistic_started'])) {
		$_SESSION['codematrix_popup_statistic_started'] = array();
	}
	if (!isset($_SESSION['codematrix_popup_statistic_action']) || !is_array($_SESSION['codematrix_popup_statistic_action'])) {
		$_SESSION['codematrix_popup_statistic_action'] = array();
	}
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
				// Фиксируем только те попапы, HTML которых действительно был выдан посетителю.
				if (!isset($_SESSION['codematrix_popup_rendered']) || !is_array($_SESSION['codematrix_popup_rendered'])) {
					$_SESSION['codematrix_popup_rendered'] = array();
				}
				$_SESSION['codematrix_popup_rendered'][$popupId] = time();
				// Новая выдача HTML начинает новый цикл статистики для этого попапа.
				unset($_SESSION['codematrix_popup_statistic_started'][$popupId], $_SESSION['codematrix_popup_statistic_action'][$popupId]);
				$popupsO->getHTMLByPopup($popupId);
			}
		}elseif($reqArr['type']=='statisticShow' || $reqArr['type']=='statisticAction'){
			// Статистика меняет данные и принимается только POST-запросом.
			$popupId=filter_var($reqArr['popupId'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
			$renderedAt = ($popupId !== false && isset($_SESSION['codematrix_popup_rendered'][$popupId]))
				? (int)$_SESSION['codematrix_popup_rendered'][$popupId]
				: 0;
			if($request->isPost() && $popupId!==false && $renderedAt>0 && $renderedAt>=time()-86400){
				if($reqArr['type']=='statisticShow'){
					// Повторный запрос при уже открытом окне не должен повторно увеличивать счётчик.
					$startedAt = (int)($_SESSION['codematrix_popup_statistic_started'][$popupId] ?? 0);
					if ($startedAt <= 0 || $startedAt > time() || $startedAt < time() - 86400) {
						$_SESSION['codematrix_popup_statistic_started'][$popupId] = time();
						unset($_SESSION['codematrix_popup_statistic_action'][$popupId]);
						$field = 'stat_show';
					}else{
						$field = '';
					}
				}else{
					$startedAt = (int)($_SESSION['codematrix_popup_statistic_started'][$popupId] ?? 0);
					$actionRecorded = !empty($_SESSION['codematrix_popup_statistic_action'][$popupId]);
					$field = ($startedAt > 0 && $startedAt <= time() && !$actionRecorded) ? 'stat_action' : '';
					if ($field !== '') {
						$_SESSION['codematrix_popup_statistic_action'][$popupId] = true;
					}
				}
				if($field !== ''){
					$retStr=$popupsO->setStatistic($popupId, 1, $field);
					$retStr=CUtil::PhpToJSObject($retStr);
					echo str_replace("'", '"', $retStr);
				}
			}
		}elseif($reqArr['type']=='statisticTime'){
			$popupId=filter_var($reqArr['popupId'], FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
			$renderedAt = ($popupId !== false && isset($_SESSION['codematrix_popup_rendered'][$popupId]))
				? (int)$_SESSION['codematrix_popup_rendered'][$popupId]
				: 0;
			$startedAt = ($popupId !== false && isset($_SESSION['codematrix_popup_statistic_started'][$popupId]))
				? (int)$_SESSION['codematrix_popup_statistic_started'][$popupId]
				: 0;
			if($request->isPost() && $popupId!==false && $renderedAt>0 && $renderedAt>=time()-86400 && $startedAt>0 && $startedAt<=time()){
				// Длительность рассчитывается по серверным часам; клиентское popupTime не является доверенным.
				$popupTime = min(86400, max(1, time() - $startedAt));
				$retStr=$popupsO->setStatistic($popupId, $popupTime, 'stat_time');
				unset($_SESSION['codematrix_popup_statistic_started'][$popupId], $_SESSION['codematrix_popup_statistic_action'][$popupId]);
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