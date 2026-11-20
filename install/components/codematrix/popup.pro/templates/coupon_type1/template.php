<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

$imageUrl = trim((string)($arResult["IMG_1_SRC"] ?? ""));
if (preg_match("/^(?:javascript|data|vbscript):/i", $imageUrl)) {
	$imageUrl = "";
}
$imageUrl = str_replace(array("\\\\", '"', "'", "(", ")", "\r", "\n"), "", $imageUrl);
$emailShow = (($arResult["EMAIL_SHOW"] ?? "N") === "Y");
$emailRequired = ($emailShow && ($arResult["EMAIL_REQUIRED"] ?? "N") === "Y");
$popupId = (int)($arParams["ID_POPUP"] ?? 0);
$ruleId = (int)($arResult["RULE_ID"] ?? 0);
$timing = (string)($arResult["TIMING"] ?? "");
$addToTable = (($arResult["EMAIL_ADD2BASE"] ?? "N") === "Y") ? "Y" : "N";
?>
<?if($arResult['TIMER']=='Y'){
		$APPLICATION->IncludeComponent('codematrix:popup.pro.timer','',array(
			'TITLE'=>$arResult['TIMER_TEXT'],
			'TIME'=>$arResult['TIMER_DATE'],
			'LEFT'=>$arResult['TIMER_LEFT'],
			'RIGHT'=>$arResult['TIMER_RIGHT'],
			'TOP'=>$arResult['TIMER_TOP'],
			'BOTTOM'=>$arResult['TIMER_BOTTOM'],
		),$component);
	}?>
<?php
function inducementWord($number, $wordArr) {
	$cases = array(2, 0, 1, 1, 1, 2);
	return $wordArr[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}
?>
<div id="codematrix_coupon_coupon">
	<div class="bg" style="background-image:url('<?=htmlspecialcharsbx($imageUrl)?>')">
		<div>
			<h2><?=htmlspecialcharsbx((string)($arResult['TITLE'] ?? ''))?></h2>
			<p><?=htmlspecialcharsbx((string)($arResult['SUBTITLE'] ?? ''))?></p>
			<span class="sale"><?=GetMessage("POPUP_SALE")?> <?=htmlspecialcharsbx((string)($arResult['PERCENT'] ?? ''))?></span><span class="avaliable"><?=GetMessage("POPUP_AVALIABLE")?> <?=htmlspecialcharsbx($timing)?>
			<?=GetMessage("POPUP_DAYS_" . inducementWord((int)$timing, array(1, 2, 5)))?>
			</span>
			<?
<?php $param_consent[] = (string)($arResult["EMAIL_TITLE"] ?? ""); ?>
				<label class="<?= $emailShow ? '' : 'notshow' ?> input">
					<p><?=GetMessage("POPUP_INFO")?><?=htmlspecialcharsbx((string)($arResult['EMAIL_PLACEHOLDER'] ?? ''))?></p>
					<input type="email" <?= $emailRequired ? 'required' : '' ?> value="<?=htmlspecialcharsbx((string)($arResult['EMAIL'] ?? ''))?>" name="EMAIL" placeholder="<?=htmlspecialcharsbx((string)($arResult['EMAIL_PLACEHOLDER'] ?? ''))?>"/>
					<span class="error"><?=GetMessage("POPUP_WRONG")?></span>
					<span class="not_new"><?=htmlspecialcharsbx((string)($arResult['EMAIL_NOT_NEW_TEXT'] ?? ''))?></span>
				</label>
			<div class="coupon_block"><button onclick="getCoupon();"><?=htmlspecialcharsbx((string)($arResult['BUTTON_TEXT'] ?? ''))?></button>
			<input class="goodCoupon" type="text" disabled>
			<span><?=GetMessage('POPUP_COPYED')?></span>
			</div>
		</div>
	</div>
</div>
<script>
	function validateEmail(elementValue){      
		var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		return emailPattern.test(elementValue); 
	} 
	function getCoupon(){
		var url = <?=json_encode($templateFolder . "/ajax.php", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>;
		url += "?id=" + encodeURIComponent(<?=json_encode((string)$ruleId)?>)
			+ "&avaliable=" + encodeURIComponent(<?=json_encode($timing)?>)
			+ "&idPopup=<?= $popupId ?>"
			+ "&addtotable=<?= $addToTable ?>";
		var email = BX('codematrix_coupon_coupon').querySelector('label.input');
		var getContinue = true;
		if (!email.classList.contains('notshow')) {
			email = email.querySelector('input');
			getContinue = validateEmail(email.value);
			url += "&email=" + encodeURIComponent(email.value);
		}
		if(getContinue){
			if(!email.classList.contains('notshow')){
				email.className="";
			}
			BX.ajax({
				url:url,
				method:'POST',
				onsuccess: function(data){
					
					if(data=='not_unique'){
						BX('codematrix_coupon_coupon').querySelector('span.not_new').style.display='inline-block';
					}else{
						BX('codematrix_coupon_coupon').querySelector('span.not_new').style.display='none';
						BX('codematrix_coupon_coupon').querySelector('label.input').remove()//.style.display='none';
				
						BX('codematrix_coupon_coupon').querySelector('div.coupon_block button').remove()//.style.display='none';
						var input=BX('codematrix_coupon_coupon').querySelector('div.coupon_block input[type="text"]');
						input.disabled=false;
						input.value=data;
						input.style.display='inline-block';
						input.addEventListener('click',function(){
							var range = document.createRange();
							range.selectNode(input);
							window.getSelection().addRange(range);
							try{
								var successful = document.execCommand('copy');
							}catch(err){
								console.log(err);
							}
							window.getSelection().removeRange(range);
							BX('codematrix_coupon_coupon').querySelector('div.coupon_block input[type="text"]+span').style.display='inline';
							<?=$arResult['BUTTON_METRIC']?>
						});
						codematrixPopupTargetAction();
					}
				},
				onfailure: function(data){
					console.log(data);
				}
			});
		}else{
			BX('codematrix_coupon_coupon').querySelector('span.not_new').style.display='none';
			email.className="error";
			email.focus();
		}
	}
</script>
