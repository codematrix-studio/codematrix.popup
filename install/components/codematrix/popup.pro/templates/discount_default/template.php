<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
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
<div class="codematrix_discount" id="codematrix_discount">
    <link href="https://fonts.googleapis.com/css?family=Old+Standard+TT" rel="stylesheet">
    <div class="background"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <div class="card">
        <?if(!empty($arResult['IMG_1_SRC'])){?><img src="<?=htmlspecialcharsbx((string)($arResult['IMG_1_SRC'] ?? ''))?>"><?}?>
        <div class="number"><?$str=str_replace('#','0',$arResult['DISCOUNT_MASK']); $str=substr_replace($str,'1',-1);?>VIP <?=htmlspecialcharsbx((string)$str)?></div>
    </div>
    <?if(!empty($arResult['IMG_2_SRC'])){?><img src="<?=htmlspecialcharsbx((string)($arResult['IMG_2_SRC'] ?? ''))?>"><?}?>
    <div class="content">
        <h2><?=htmlspecialcharsbx((string)($arResult['TITLE'] ?? ''))?></h2>
        <p><?=htmlspecialcharsbx((string)($arResult['SUBTITLE'] ?? ''))?></p>
        <form method="POST" onsubmit="getCoupon(this);return false;">
        <fieldset>
			<?
			if($arResult['NAME_SHOW']=='Y'){
				if($arResult['NAME_REQUIRED']=='N' || $arResult['NAME_REQUIRED']=='Y'){
					$arResult['NAME_REQUIRED']=($arResult['NAME_REQUIRED']=='N')?'':'required';
				}
            }
			if($arResult['NAME_SHOW']=='N' || $arResult['NAME_SHOW']=='Y'){
				$arResult['NAME_SHOW']=($arResult['NAME_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['NAME_TITLE'];
			}?>
			<label class="one <?=$arResult['NAME_SHOW']?> <?=$arResult['NAME_REQUIRED']?>">
				<input <?=$arResult['NAME_REQUIRED']?> name="NAME" type="text" value="<?=htmlspecialcharsbx((string)($arResult['NAME'] ?? ''))?>" placeholder="<?=htmlspecialcharsbx((string)($arResult['NAME_TITLE'] ?? ''))?>" />
				<span><sup>*</sup></span>
			</label>
            <?
			if($arResult['LASTNAME_SHOW']=='Y'){
				if($arResult['LASTNAME_REQUIRED']=='N' || $arResult['LASTNAME_REQUIRED']=='Y'){
					$arResult['LASTNAME_REQUIRED']=($arResult['LASTNAME_REQUIRED']=='N')?'':'required';
				}
			}
			if($arResult['LASTNAME_SHOW']=='N' || $arResult['LASTNAME_SHOW']=='Y'){
				$arResult['LASTNAME_SHOW']=($arResult['LASTNAME_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['LASTNAME_TITLE'];
			}?>
			<label class="one <?=$arResult['LASTNAME_SHOW']?> <?=$arResult['LASTNAME_REQUIRED']?>">
				<input <?=$arResult['LASTNAME_REQUIRED']?> name="LASTNAME" type="text" value="<?=htmlspecialcharsbx((string)($arResult['LASTNAME'] ?? ''))?>" placeholder="<?=htmlspecialcharsbx((string)($arResult['LASTNAME_TITLE'] ?? ''))?>" />
				<span><sup>*</sup></span>
			</label>
			<?
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
			<label class="one <?=$arResult['PHONE_SHOW']?> <?=$arResult['PHONE_REQUIRED']?>">
				<input <?=$arResult['PHONE_REQUIRED']?> type="text" value="<?=htmlspecialcharsbx((string)($arResult['PHONE'] ?? ''))?>" name="PHONE" placeholder="<?=htmlspecialcharsbx((string)($arResult['PHONE_TITLE'] ?? ''))?>" />
				<span><sup>*</sup></span>
			</label>
			<?
			if($arResult['EMAIL_SHOW']=='Y'){
				if($arResult['EMAIL_REQUIRED']=='N' || $arResult['EMAIL_REQUIRED']=='Y'){
					$arResult['EMAIL_REQUIRED']=($arResult['EMAIL_REQUIRED']=='N')?'':'required';
				}
			}
			if($arResult['EMAIL_SHOW']=='N' || $arResult['EMAIL_SHOW']=='Y'){
				$arResult['EMAIL_SHOW']=($arResult['EMAIL_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['EMAIL_TITLE'];
			}?>
            <span class="not_new"><?=GetMessage('flp.not_new')?></span>
            <span class="not_valid"><?=htmlspecialcharsbx((string)($arResult['EMAIL_NOT_NEW_TEXT'] ?? ''))?></span>
			<label class="<?=$arResult['EMAIL_SHOW']?> <?=$arResult['EMAIL_REQUIRED']?> one email">
				<input <?=$arResult['EMAIL_REQUIRED']?> type="email" value="<?=htmlspecialcharsbx((string)($arResult['EMAIL'] ?? ''))?>" name="EMAIL" placeholder="<?=htmlspecialcharsbx((string)($arResult['EMAIL_TITLE'] ?? ''))?>" />
				<span><sup>*</sup></span>
			</label>

			
			<label class="submit"><input type="submit" onclick=""  value="<?=htmlspecialcharsbx((string)($arResult['BUTTON_TEXT'] ?? ''))?>"></label>
			<?
			if($arResult['USE_CONSENT_SHOW']=='N' || $arResult['USE_CONSENT_SHOW']=='Y'){
				$arResult['USE_CONSENT_SHOW']=($arResult['USE_CONSENT_SHOW']=='N')?'notshow':'';
			}
			if($arResult['USE_CONSENT_SHOW']!='N' && count($arResult['AGREEMENTS'])>0){
			?>
			<div class="<?=$arResult['USE_CONSENT_SHOW']?> consentBlock">
			<input type="checkbox" name="use_consent" value="Y" checked="checked" required /> <a href="/bitrix/tools/codematrix_agreement.php?ID=<?= (int)($arResult['CONSENT_ID'] ?? 0) ?>" target="_blank"><?=htmlspecialcharsbx((string)($arResult['CONSENT_LIST'] ?? ''))?></a>
			</div>
			<?}?>
			<div class="clear"></div>
		</fieldset>
        </form>
    </div>
</div>
<script>
	function validateEmail(elementValue){      
		var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		return emailPattern.test(elementValue); 
	} 
	function getCoupon(f){
		var url = <?=json_encode($templateFolder . "/ajax.php", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>;
		var email=f.querySelector('label.email');
        var name=f.querySelector('input[name="NAME"]').value;
        var lastname=f.querySelector('input[name="LASTNAME"]').value;
        var phone=f.querySelector('input[name="PHONE"]').value;
		var getContinue=true;
		email = email.querySelector('input');
		getContinue=validateEmail(email.value);
		url+="?idPopup=<?= (int)$arParams['ID_POPUP'] ?>&addtotable="+encodeURIComponent(<?=json_encode((string)$arResult['EMAIL_ADD2BASE'])?>);
		url+="&email="+encodeURIComponent(email.value);
        url+='&NAME='+encodeURIComponent(name)+'&LAST_NAME='+encodeURIComponent(lastname)+'&PHONE='+encodeURIComponent(phone);
		if(getContinue){
			if(!email.classList.contains('notshow')){
				email.className="";
			}
			BX.ajax({
				url:url,
				method:'POST',
				onsuccess: function(data){
					console.log(data);
					if(data=='not_unique'){
						f.querySelector('span.not_new').style.display='inline-block';
					}else{
						f.querySelector('span.not_new').style.display='none';
						f.querySelector('span.not_valid').style.display='none';
						f.remove()//.style.display='none';
						var element = document.createElement('div');
                        element.setAttribute('class','send');
                        var t = document.createTextNode(<?=json_encode(GetMessage('flp.discount_send'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>);
                        element.appendChild(t);
                        document.querySelector('#codematrix_discount div.content').appendChild(element);
						codematrixPopupTargetAction();
                        <?=$arResult['BUTTON_METRIC']?>
					}
				},
				onfailure: function(data){
					console.log(data);
				}
			});
		}else{
			f.querySelector('span.not_new').style.display='none';
			f.querySelector('span.not_valid').style.display='block';
			email.className="error";
			email.focus();
		}
	}
</script>