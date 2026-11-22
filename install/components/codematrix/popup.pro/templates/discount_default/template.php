<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?php
// В заголовке разрешена только базовая HTML-разметка оформления.
// Атрибуты тегов удаляются, чтобы сохранить форматирование без риска внедрения скриптов.
$discountTitle = (string)($arResult['TITLE'] ?? '');
$discountTitle = preg_replace_callback(
    '~</?([a-z][a-z0-9]*)\\b[^>]*>~i',
    function ($matches) {
        $tag = strtolower($matches[1]);

        if ($tag === 'span') {
            return (strpos($matches[0], '</') === 0) ? '</span>' : '<span>';
        }

        if ($tag === 'br') {
            return '<br>';
        }

        return '';
    },
    $discountTitle
);
?>
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
<div class="codematrix_discount" id="codematrix_discount">
    <link href="https://fonts.googleapis.com/css?family=Old+Standard+TT" rel="stylesheet">
    <div class="background"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <div class="card">
        <?php if(!empty($arResult['IMG_1_SRC'])){?><img src="<?=htmlspecialcharsbx((string)($arResult['IMG_1_SRC'] ?? ''))?>"><?php }?>
        <div class="number"><?php $str=str_replace('#','0',$arResult['DISCOUNT_MASK']); $str=substr_replace($str,'1',-1);?>VIP <?=htmlspecialcharsbx((string)$str)?></div>
    </div>
    <?php if(!empty($arResult['IMG_2_SRC'])){?><img src="<?=htmlspecialcharsbx((string)($arResult['IMG_2_SRC'] ?? ''))?>"><?php }?>
    <div class="content">
        <h2><?=$discountTitle?></h2>
        <?php
        // В описании разрешено только безопасное форматирование; атрибуты и остальные теги удаляются.
        $discountSubtitle = html_entity_decode(
            (string)($arResult['SUBTITLE'] ?? ''),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
        $discountSubtitle = preg_replace_callback(
            '~</?([a-z][a-z0-9]*)\\b[^>]*>~i',
            static function ($matches) {
                $tag = strtolower($matches[1]);

                if ($tag === 'b' || $tag === 'strong' || $tag === 'i' || $tag === 'em') {
                    return (strpos($matches[0], '</') === 0) ? '</' . $tag . '>' : '<' . $tag . '>';
                }

                if ($tag === 'br') {
                    return '<br>';
                }

                return '';
            },
            $discountSubtitle
        );
        ?>
        <p><?=$discountSubtitle?></p>
        <form method="POST" onsubmit="getCoupon(this);return false;">
        <fieldset>
			<?php
			if($arResult['NAME_SHOW']=='Y'){
				if($arResult['NAME_REQUIRED']=='N' || $arResult['NAME_REQUIRED']=='Y'){
					$arResult['NAME_REQUIRED']=($arResult['NAME_REQUIRED']=='N')?'':'required';
				}
            }
			if($arResult['NAME_SHOW']=='N' || $arResult['NAME_SHOW']=='Y'){
				$arResult['NAME_SHOW']=($arResult['NAME_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['NAME_TITLE'];
			}?>
			<label class="one <?= (($arResult['NAME_SHOW'] ?? '') === 'notshow') ? 'notshow' : '' ?> <?= (($arResult['NAME_REQUIRED'] ?? '') === 'required') ? 'required' : '' ?>">
				<input <?= (($arResult['NAME_REQUIRED'] ?? '') === 'required') ? 'required' : '' ?> name="NAME" type="text" value="<?=htmlspecialcharsbx((string)($arResult['NAME'] ?? ''))?>" placeholder="<?=htmlspecialcharsbx((string)($arResult['NAME_TITLE'] ?? ''))?>" />
				<span><sup>*</sup></span>
			</label>
            <?php
			if($arResult['LASTNAME_SHOW']=='Y'){
				if($arResult['LASTNAME_REQUIRED']=='N' || $arResult['LASTNAME_REQUIRED']=='Y'){
					$arResult['LASTNAME_REQUIRED']=($arResult['LASTNAME_REQUIRED']=='N')?'':'required';
				}
			}
			if($arResult['LASTNAME_SHOW']=='N' || $arResult['LASTNAME_SHOW']=='Y'){
				$arResult['LASTNAME_SHOW']=($arResult['LASTNAME_SHOW']=='N')?'notshow':'';
				$param_consent[]=$arResult['LASTNAME_TITLE'];
			}?>
			<label class="one <?= (($arResult['LASTNAME_SHOW'] ?? '') === 'notshow') ? 'notshow' : '' ?> <?= (($arResult['LASTNAME_REQUIRED'] ?? '') === 'required') ? 'required' : '' ?>">
				<input <?= (($arResult['LASTNAME_REQUIRED'] ?? '') === 'required') ? 'required' : '' ?> name="LASTNAME" type="text" value="<?=htmlspecialcharsbx((string)($arResult['LASTNAME'] ?? ''))?>" placeholder="<?=htmlspecialcharsbx((string)($arResult['LASTNAME_TITLE'] ?? ''))?>" />
				<span><sup>*</sup></span>
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
			<label class="one <?= (($arResult['PHONE_SHOW'] ?? '') === 'notshow') ? 'notshow' : '' ?> <?= (($arResult['PHONE_REQUIRED'] ?? '') === 'required') ? 'required' : '' ?>">
				<input <?= (($arResult['PHONE_REQUIRED'] ?? '') === 'required') ? 'required' : '' ?> type="text" value="<?=htmlspecialcharsbx((string)($arResult['PHONE'] ?? ''))?>" name="PHONE" placeholder="<?=htmlspecialcharsbx((string)($arResult['PHONE_TITLE'] ?? ''))?>" />
				<span><sup>*</sup></span>
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
			}?>
            <span class="not_new"><?=GetMessage('flp.not_new')?></span>
            <span class="not_valid"><?=htmlspecialcharsbx((string)($arResult['EMAIL_NOT_NEW_TEXT'] ?? ''))?></span>
            <span class="auth_required" style="display:none;"><?=GetMessage('flp.auth_required')?></span>
            <span class="consent_required" style="display:none;">Необходимо подтвердить согласие на обработку персональных данных.</span>
            <span class="coupon_error" style="display:none;"><?=GetMessage('flp.coupon_error')?></span>
			<label class="<?= (($arResult['EMAIL_SHOW'] ?? '') === 'notshow') ? 'notshow' : '' ?> <?= (($arResult['EMAIL_REQUIRED'] ?? '') === 'required') ? 'required' : '' ?> one email">
				<input <?= (($arResult['EMAIL_REQUIRED'] ?? '') === 'required') ? 'required' : '' ?> type="email" value="<?=htmlspecialcharsbx((string)($arResult['EMAIL'] ?? ''))?>" name="EMAIL" placeholder="<?=htmlspecialcharsbx((string)($arResult['EMAIL_TITLE'] ?? ''))?>" />
				<span><sup>*</sup></span>
			</label>

			
			<label class="submit"><input type="submit" onclick=""  value="<?=htmlspecialcharsbx((string)($arResult['BUTTON_TEXT'] ?? ''))?>"></label>
			<?php
			if($arResult['USE_CONSENT_SHOW']=='N' || $arResult['USE_CONSENT_SHOW']=='Y'){
				$arResult['USE_CONSENT_SHOW']=($arResult['USE_CONSENT_SHOW']=='N')?'notshow':'';
			}
			if($arResult['USE_CONSENT_SHOW']!='N' && !empty($arResult['AGREEMENTS']) && is_array($arResult['AGREEMENTS']) && count($arResult['AGREEMENTS'])>0){
			?>
			<div class="<?= (($arResult['USE_CONSENT_SHOW'] ?? '') === 'notshow') ? 'notshow' : '' ?> consentBlock">
			<input type="checkbox" name="use_consent" value="Y" checked="checked" required /> <a href="/bitrix/tools/codematrix_agreement.php?ID=<?= (int)($arResult['CONSENT_ID'] ?? 0) ?>" target="_blank"><?=htmlspecialcharsbx((string)($arResult['CONSENT_LIST'] ?? ''))?></a>
			</div>
			<?php }?>
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
		var data = {
			idPopup: <?= (int)$arParams['ID_POPUP'] ?>,
			addtotable: <?=json_encode((string)$arResult['EMAIL_ADD2BASE'])?>,
			use_consent: (f.querySelector('input[name="use_consent"]') && f.querySelector('input[name="use_consent"]').checked) ? 'Y' : 'N',
			email: email.value,
			NAME: name,
			LAST_NAME: lastname,
			PHONE: phone
		};
		if(getContinue){
			if(!email.classList.contains('notshow')){
				email.className="";
			}
			BX.ajax({
				url:url,
				data:data,
				method:'POST',
				onsuccess: function(data){
					console.log(data);
					if(data=='not_unique'){
						f.querySelector('span.not_new').style.display='inline-block';
					}else if(data=='auth_required'){
						f.querySelector('span.auth_required').style.display='inline-block';
						return;
					}else if(data=='consent_required'){
						f.querySelector('span.consent_required').style.display='inline-block';
						return;
					}else if(data=='coupon_error'){
						f.querySelector('span.coupon_error').style.display='inline-block';
						return;
					}else{
						f.querySelector('span.auth_required').style.display='none';
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