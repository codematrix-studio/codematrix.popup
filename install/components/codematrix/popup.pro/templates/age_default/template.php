<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

$hrefLink = trim((string)($arResult["HREF_LINK"] ?? ""));
if (preg_match("/^(?:javascript|data|vbscript):/i", $hrefLink)) {
	$hrefLink = "#";
}
$popupId = (int)($arParams["ID_POPUP"] ?? 0);
?>
<div id="codematrix_age_default">
	<img src="<?= htmlspecialcharsbx((string)($arResult["IMG_1_SRC"] ?? "")) ?>" alt="">
	<h2><?= htmlspecialcharsbx((string)($arResult["TITLE"] ?? "")) ?></h2>
	<div class="buttons">
		<a rel="nofollow" href="javascript:void(0);" class="flTargetAction yesClick"><?= htmlspecialcharsbx((string)($arResult["BUTTON_TEXT_Y"] ?? "")) ?></a>
		<a rel="nofollow" href="<?= htmlspecialcharsbx($hrefLink) ?>" class="noClick"><?= htmlspecialcharsbx((string)($arResult["BUTTON_TEXT_N"] ?? "")) ?></a>
	</div>
	<script>
		setTimeout(function () {
			var tmpEl = document.querySelector('#codematrix_age_default .flTargetAction');
			if (!tmpEl) {
				return;
			}
			tmpEl.addEventListener("click", function () {
				BX.ajax({
					url: '<?= CUtil::JSEscape($templateFolder) ?>/ajax.php',
					data: {
						id_popup: '<?= $popupId ?>',
						checked: 'Y'
					},
					method: 'POST',
					dataType: 'html',
					scriptsRunFirst: false,
					timeout: 300,
					onsuccess: function () {
						codematrixPopups.currentPopup.close();
					},
					onfailure: function (data) {
						console.log(data);
					}
				});
			});
		}, 50);
	</script>
</div>
