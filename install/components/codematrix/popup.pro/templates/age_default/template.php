<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

$linkHref = trim((string)($arResult["HREF_LINK"] ?? ""));
if (
	preg_match("/^[a-z][a-z0-9+.-]*:/i", $linkHref)
	&& !preg_match("/^https?:\\/\\//i", $linkHref)
) {
	$linkHref = "#";
}
$popupId = (int)($arParams["ID_POPUP"] ?? 0);
?>
<div id="codematrix_age_default">
	<img src="<?= htmlspecialcharsbx((string)($arResult["IMG_1_SRC"] ?? "")) ?>">
	<h2><?= htmlspecialcharsbx((string)($arResult["TITLE"] ?? "")) ?></h2>
	<div class="buttons">
		<a rel="nofollow" href="javascript:void(0);" class="flTargetAction yesClick"><?= htmlspecialcharsbx((string)($arResult["BUTTON_TEXT_Y"] ?? "")) ?></a>
		<a rel="nofollow" href="<?= htmlspecialcharsbx($linkHref) ?>" class="noClick"><?= htmlspecialcharsbx((string)($arResult["BUTTON_TEXT_N"] ?? "")) ?></a>
	</div>
	<script>
		setTimeout(function () {
			var tmpEl = document.querySelector('#codematrix_age_default .flTargetAction');
			if (!tmpEl) {
				return;
			}

			tmpEl.addEventListener("click", pop_close);

			function pop_close() {
				BX.ajax({
					url: <?= json_encode($templateFolder . "/ajax.php", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
					data: {
						id_popup: <?= $popupId ?>,
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
			}
		}, 50);
	</script>
</div>
