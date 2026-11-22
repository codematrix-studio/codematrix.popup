<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

$htmlContent = (string)($arResult['TEXTAREA'] ?? '');
// HTML-шаблон должен интерпретировать разметку, в том числе сохранённую
// в старых записях в виде HTML-сущностей (&lt;div&gt;).
$htmlContent = html_entity_decode($htmlContent, ENT_QUOTES | ENT_HTML5, defined('SITE_CHARSET') ? SITE_CHARSET : 'UTF-8');
?>
<?php if (($arResult['TIMER'] ?? '') === 'Y') {
	$APPLICATION->IncludeComponent('codematrix:popup.pro.timer', '', array(
		'TITLE' => $arResult['TIMER_TEXT'] ?? '',
		'TIME' => $arResult['TIMER_DATE'] ?? '',
		'LEFT' => $arResult['TIMER_LEFT'] ?? '',
		'RIGHT' => $arResult['TIMER_RIGHT'] ?? '',
		'TOP' => $arResult['TIMER_TOP'] ?? '',
		'BOTTOM' => $arResult['TIMER_BOTTOM'] ?? '',
	), $component);
} ?>
<div id="codematrix_banner_default">
	<?= $htmlContent ?>
</div>
