<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

$linkHref = trim((string)($arResult["LINK_HREF"] ?? ""));
if (preg_match("/^(?:javascript|data|vbscript):/i", $linkHref)) {
	$linkHref = "#";
}
$hrefTarget = in_array(($arResult["HREF_TARGET"] ?? ""), array("_blank", "_self"), true)
	? $arResult["HREF_TARGET"]
	: "_self";
?>
<div id="codematrix_banner_default">
	<?php if (($arResult["TIMER"] ?? "") === "Y") {
		$APPLICATION->IncludeComponent('codematrix:popup.pro.timer', '', array(
			'TITLE' => $arResult['TIMER_TEXT'] ?? '',
			'TIME' => $arResult['TIMER_DATE'] ?? '',
			'LEFT' => $arResult['TIMER_LEFT'] ?? '',
			'RIGHT' => $arResult['TIMER_RIGHT'] ?? '',
			'TOP' => $arResult['TIMER_TOP'] ?? '',
			'BOTTOM' => $arResult['TIMER_BOTTOM'] ?? '',
		), $component);
	} ?>
	<a class="flTargetAction" href="<?= htmlspecialcharsbx($linkHref) ?>" target="<?= htmlspecialcharsbx($hrefTarget) ?>">
		<img src="<?= htmlspecialcharsbx((string)($arResult['IMG_1_SRC'] ?? '')) ?>">
	</a>
</div>
