<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
// Повторно получаем данные акции непосредственно в шаблоне,
// если компонент передал пустой результат (например, при нестандартном вызове).
if (
    (!isset($arResult['TITLE']) || trim((string)$arResult['TITLE']) === '')
    && !empty($arParams['ID_POPUP'])
    && class_exists('popup')
) {
    $templatePopup = new popup();
    $templateResult = $templatePopup->getComponentResult((int)$arParams['ID_POPUP']);
    if (is_array($templateResult)) {
        $arResult = array_replace($arResult, $templateResult);
    }
}
?>
<div id="codematrix_popup_action">
	<?php if($arResult['TIMER']=='Y'){
		$APPLICATION->IncludeComponent('codematrix:popup.pro.timer','',array(
			'TITLE'=>$arResult['TIMER_TEXT'],
			'TIME'=>$arResult['TIMER_DATE'],
			'LEFT'=>$arResult['TIMER_LEFT'],
			'RIGHT'=>$arResult['TIMER_RIGHT'],
			'TOP'=>$arResult['TIMER_TOP'],
			'BOTTOM'=>$arResult['TIMER_BOTTOM'],
		),$component);
	}?>
	<img id="img_going_s1" src="<?=$arResult['IMG_1_SRC']?>">
	<div class="text">
		<h2><?=$arResult['TITLE']?></h2>
		<div class="info"><?=$arResult['CONTENT']?></div><br>
		<h3><?=$arResult['SUBTITLE']?></h3>
		<a onclick="<?=$arResult['BUTTON_METRIC']?>" target="<?=$arResult['HREF_TARGET']?>" href="<?=$arResult['LINK_HREF']?>" class="flTargetAction going_link"><?=$arResult['LINK_TEXT']?></a>
	</div>
</div>