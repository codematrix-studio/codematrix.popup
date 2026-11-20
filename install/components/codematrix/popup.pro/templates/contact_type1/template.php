<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<div id="codematrix_contact_type1">
	
	<div class="bgColorBlock">
	<?php if(!empty($arResult['ERRORS'])){?>
		<div class="error"><p>Error:</p>
		<?php foreach($arResult['ERRORS'] as $nextError){?>
			<p><?=htmlspecialcharsbx((string)$nextError)?></p>
		<?php }?>
		</div>
	<?php }if(empty($arResult['SUCCESS'])){
		$param_consent=array();?>
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
	<form action="<?=htmlspecialcharsbx((string)$templateFolder)?>/ajax.php" method="POST" onsubmit="sendForm1(this);return false;">
		<input type="hidden" name="id_popup" value="<?= (int)$arParams['ID_POPUP'] ?>" />
		<input type="hidden" name="template_name" value="<?=$templateName?>" />
			<img id="img_going_s1" src="<?=htmlspecialcharsbx((string)($arResult['IMG_1_SRC'] ?? ''))?>">
			<h2><?=htmlspecialcharsbx((string)($arResult['TITLE'] ?? ''))?></h2>
			<h3><?=htmlspecialcharsbx((string)($arResult['SUBTITLE'] ?? ''))?></h3>
		<div class="fieldset">
			<?php
			if($arResult['NAME_SHOW']=='Y'){
				if($arResult['NAME_REQUIRED']=='N' || $arResult['NAME_REQUIRED']=='Y'){
					$arResult['NAME_REQUIRED']=($arResult['NAME_REQUIRED']=='N')?'':'required';
				}
			}
			if($arResult['NAME_SHOW']=='N' || $arResult['NAME_SHOW']=='Y'){
				$arResult['NAME_SHOW']=($arResult['NAME_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['NAME_TITLE'];
			}
			?>
			<label class="<?=htmlspecialcharsbx((string)($arResult['NAME_SHOW'] ?? ''))?> <?=htmlspecialcharsbx((string)($arResult['NAME_REQUIRED'] ?? ''))?> input">
				<input <?=htmlspecialcharsbx((string)($arResult['NAME_REQUIRED'] ?? ''))?> name="NAME" type="text" value="<?=htmlspecialcharsbx((string)($arResult['NAME'] ?? ''))?>" placeholder="<?=htmlspecialcharsbx((string)($arResult['NAME_PLACEHOLDER'] ?? ''))?>"/>
				<span><?=htmlspecialcharsbx((string)($arResult['NAME_TITLE'] ?? ''))?><sup>*</sup></span>
			</label>
			<?php
			if($arResult['PHONE_SHOW']=='Y'){
				if($arResult['PHONE_REQUIRED']=='N' || $arResult['PHONE_REQUIRED']=='Y'){
					$arResult['PHONE_REQUIRED']=($arResult['PHONE_REQUIRED']=='N')?'':'required';
				}
			}
			if($arResult['PHONE_SHOW']=='N' || $arResult['PHONE_SHOW']=='Y'){
				$arResult['PHONE_SHOW']=($arResult['PHONE_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['PHONE_TITLE'];
			}
			?>
			<label class="<?=htmlspecialcharsbx((string)($arResult['PHONE_SHOW'] ?? ''))?> <?=htmlspecialcharsbx((string)($arResult['PHONE_REQUIRED'] ?? ''))?> input">
				<input <?=htmlspecialcharsbx((string)($arResult['PHONE_REQUIRED'] ?? ''))?> type="text" value="<?=htmlspecialcharsbx((string)($arResult['PHONE'] ?? ''))?>" name="PHONE" placeholder="<?=htmlspecialcharsbx((string)($arResult['PHONE_PLACEHOLDER'] ?? ''))?>"/>
				<span><?=htmlspecialcharsbx((string)($arResult['PHONE_TITLE'] ?? ''))?><sup>*</sup></span>
			</label>
			<?php
			if($arResult['EMAIL_SHOW']=='Y'){
				if($arResult['EMAIL_REQUIRED']=='N' || $arResult['EMAIL_REQUIRED']=='Y'){
					$arResult['EMAIL_REQUIRED']=($arResult['EMAIL_REQUIRED']=='N')?'':'required';
				}
			}
			if($arResult['EMAIL_SHOW']=='N' || $arResult['EMAIL_SHOW']=='Y'){
				$arResult['EMAIL_SHOW']=($arResult['EMAIL_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['EMAIL_TITLE'];
			}
			?>
			<label class="<?=htmlspecialcharsbx((string)($arResult['EMAIL_SHOW'] ?? ''))?> <?=htmlspecialcharsbx((string)($arResult['EMAIL_REQUIRED'] ?? ''))?> input">
				<input <?=htmlspecialcharsbx((string)($arResult['EMAIL_REQUIRED'] ?? ''))?> type="text" value="<?=htmlspecialcharsbx((string)($arResult['EMAIL'] ?? ''))?>" name="EMAIL" placeholder="<?=htmlspecialcharsbx((string)($arResult['EMAIL_PLACEHOLDER'] ?? ''))?>"/>
				<span><?=htmlspecialcharsbx((string)($arResult['EMAIL_TITLE'] ?? ''))?><sup>*</sup></span>
			</label>
		</div>
		<div class="fieldset last">
			<?php
			if($arResult['DESCRIPTION_SHOW']=='Y'){
				if($arResult['DESCRIPTION_REQUIRED']=='N' || $arResult['DESCRIPTION_REQUIRED']=='Y'){
					$arResult['DESCRIPTION_REQUIRED']=($arResult['DESCRIPTION_REQUIRED']=='N')?'':'required';
				}
			}
			if($arResult['DESCRIPTION_SHOW']=='N' || $arResult['DESCRIPTION_SHOW']=='Y'){
				$arResult['DESCRIPTION_SHOW']=($arResult['DESCRIPTION_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['DESCRIPTION_TITLE'];
				?>
				
			<?php }?>
			<label class="<?=htmlspecialcharsbx((string)($arResult['DESCRIPTION_SHOW'] ?? ''))?> <?=htmlspecialcharsbx((string)($arResult['DESCRIPTION_REQUIRED'] ?? ''))?> textarea">
				<textarea <?=htmlspecialcharsbx((string)($arResult['DESCRIPTION_REQUIRED'] ?? ''))?> name="DESCRIPTION" placeholder="<?=htmlspecialcharsbx((string)($arResult['DESCRIPTION_PLACEHOLDER'] ?? ''))?>"><?=htmlspecialcharsbx((string)($arResult['DESCRIPTION'] ?? ''))?></textarea>
				<span><?=htmlspecialcharsbx((string)($arResult['DESCRIPTION_TITLE'] ?? ''))?><sup>*</sup></span>
			</label>
			
			<label class="submit">
				<button type="submit" onclick=""><?=htmlspecialcharsbx((string)($arResult['BUTTON_TEXT'] ?? ''))?></button>
			</label>
			<?php
				if($arResult['USE_CONSENT_SHOW']=='N' || $arResult['USE_CONSENT_SHOW']=='Y'){
					$arResult['USE_CONSENT_SHOW']=($arResult['USE_CONSENT_SHOW']=='N')?'notshow':'';
				}
			if($arResult['USE_CONSENT_SHOW']!='N' && count($arResult['AGREEMENTS'])>0){
			?>
			<div class="<?=htmlspecialcharsbx((string)($arResult['USE_CONSENT_SHOW'] ?? ''))?> consentBlock">
			<input type="checkbox" name="use_consent" value="Y" checked="checked" required /> <a href="/bitrix/tools/codematrix_agreement.php?ID=<?= (int)$arResult['CONSENT_ID'] ?>" target="_blank"><?=htmlspecialcharsbx((string)($arResult['CONSENT_LIST'] ?? ''))?></a>
			</div>
			<?php }?>
			<div class="clear"></div>
		</div>
	</form>
	<script>
		function sendForm1(f){
			var sendO={},
				cInputs=f.querySelectorAll("input, textarea");
			for(var i=0; i<cInputs.length; i++){
				if(cInputs[i].type==='checkbox' && !cInputs[i].checked){
					continue;
				}
				sendO[cInputs[i].name]=cInputs[i].value;
			}
			BX.ajax({
				url: f.action,
				data:sendO,
				method: 'POST',
				dataType: 'html',
				scriptsRunFirst:false,
				timeout:300,
				onsuccess: function(data){
					BX("codematrix_contact_type1").outerHTML=data;
					<?=$arResult['BUTTON_METRIC']?>
				},
				onfailure: function(data){
					console.log(data);
				}
			});
		}
	</script>
	<?php }if(!empty($arResult['SUCCESS']) && $arResult['SUCCESS']=='Y'){?>
	<div class="success"><?=GetMessage("POPUP_THANKS")?></div>
	<script>
		BX.ready(function(){
			positionBanner();
		});
	</script>
	<?php }?>
	<div>
</div>