<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$rouletteElements = (isset($arResult['ELEMENTS']) && is_array($arResult['ELEMENTS'])) ? $arResult['ELEMENTS'] : array();
$rouletteElementsCount = count($rouletteElements);
$deg = $rouletteElementsCount > 0 ? 100 / $rouletteElementsCount : 0;
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
<div id="codematrix_roulette">

    <h2><?=htmlspecialcharsbx((string)($arResult['TITLE'] ?? ''))?></h2>
    <h3><?=htmlspecialcharsbx((string)($arResult['SUBTITLE'] ?? ''))?></h3>
     <div class="contain_roulette">
      <div class="rotate_shadow">
      <div class="rotate_block">
        <section class="container">
        </section>
        <div class="text">
        </div>
      </div>
      </div>
      <div class="arrow">
        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
<path style="fill:#FD003A;" d="M256,0C156.698,0,76,80.7,76,180c0,33.6,9.302,66.301,27.001,94.501l140.797,230.414
	c2.402,3.9,6.002,6.301,10.203,6.901c5.698,0.899,12.001-1.5,15.3-7.2l141.2-232.516C427.299,244.501,436,212.401,436,180
	C436,80.7,355.302,0,256,0z M256,270c-50.398,0-90-40.8-90-90c0-49.501,40.499-90,90-90s90,40.499,90,90
	C346,228.9,306.999,270,256,270z"/>
<path style="fill:#E50027;" d="M256,0v90c49.501,0,90,40.499,90,90c0,48.9-39.001,90-90,90v241.991
	c5.119,0.119,10.383-2.335,13.3-7.375L410.5,272.1c16.799-27.599,25.5-59.699,25.5-92.1C436,80.7,355.302,0,256,0z"/>
<g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
</svg>
      </div>
    </div>
    <script>
        function validateEmail(elementValue){
        	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        	return emailPattern.test(elementValue);
        }

        var dataset = [
            <?php foreach($rouletteElements as $element){?>
                {
                    value:<?=$deg?>,
                    color:<?=json_encode((string)$element['color'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>,
                    text:<?=json_encode((string)$element['text'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>,
                    rule:<?=json_encode((string)$element['rule'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>
                },
            <?php }?>
];

var maxValue = 25;
var rouletteBusy = false;

function rouletteRequest(data, callback){
	var url = <?=json_encode($templateFolder . "/ajax.php", JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>;
	BX.ajax({
		url: url,
		method: 'POST',
		dataType: 'json',
		data: data,
		timeout: 30000,
		onsuccess: callback,
		onfailure: function(){
			rouletteBusy = false;
			BX('codematrix_roulette').querySelector('p.result_roll_coupon_error').style.display = 'inline-block';
			BX('codematrix_roulette').querySelector('button.roll_roulette').disabled = false;
		}
	});
}

function roll_roulette_func(count){
	if(rouletteBusy){
		return;
	}

	var root = BX('codematrix_roulette');
	var emailField = root.querySelector('label.email input');
	var email = emailField.value.trim();

	if(!validateEmail(email)){
		var emailLabel = root.querySelector('label.email');
		emailLabel.className += ' error';
		emailField.focus();
		return;
	}

	rouletteBusy = true;
	root.querySelector('button.roll_roulette').disabled = true;
	root.querySelector('p.result_roll_not_unique').style.display = 'none';
	root.querySelector('p.result_roll_coupon_error').style.display = 'none';

	rouletteRequest({
		mode: 'draw',
		idPopup: <?= (int)$arParams['ID_POPUP'] ?>,
		email: email,
		addtotable: <?=json_encode((string)$arResult['EMAIL_ADD2BASE'])?>
	}, function(data){
		if(!data || data.status !== 'ok' || !Number.isInteger(data.index) || data.index < 0 || data.index >= count || typeof data.token !== 'string'){
			rouletteBusy = false;
			root.querySelector('button.roll_roulette').disabled = false;
			if(data && data.code === 'not_unique'){
				root.querySelector('p.result_roll_not_unique').style.display = 'inline-block';
			}else{
				root.querySelector('p.result_roll_coupon_error').style.display = 'inline-block';
			}
			return;
		}

		spinRoulette(data.index, data.token, email, count);
	});
}

function spinRoulette(index, token, email, count){
	var percent = 100 / count;
	var deg = 360 / 100;
	var center = 90 - (percent * 3.6 / 2);
	var res = -(deg * percent * index - center);

	document.querySelector('#codematrix_roulette div.rotate_block').style.transform = 'rotate(' + (res - 3600) + 'deg)';
	setTimeout(function(){
		res_roll(index + 1, token, email);
	}, 7000);
}

function res_roll(sector, token, email){
	var root = BX('codematrix_roulette');
	var selected = root.querySelector('div.rotate_block>.text>div:nth-child(' + sector + ')');
	if(!selected){
		rouletteBusy = false;
		root.querySelector('button.roll_roulette').disabled = false;
		return;
	}

	var not = root.querySelectorAll('div.rotate_block>.container>div:not(:nth-child(' + sector + '))');
	for(var i = 0; i < not.length; i++){
		not[i].className += ' negative';
	}

	rouletteRequest({
		mode: 'redeem',
		idPopup: <?= (int)$arParams['ID_POPUP'] ?>,
		token: token,
		email: email,
		addtotable: <?=json_encode((string)$arResult['EMAIL_ADD2BASE'])?>
	}, function(data){
		if(!data || data.status !== 'success'){
			rouletteBusy = false;
			root.querySelector('button.roll_roulette').disabled = false;
			if(data && data.code === 'not_unique'){
				root.querySelector('p.result_roll_not_unique').style.display = 'inline-block';
			}else{
				root.querySelector('p.result_roll_coupon_error').style.display = 'inline-block';
			}
			return;
		}

		rouletteBusy = false;
		if(data.rule === 'nothing'){
			root.querySelector('label.input').remove();
			root.querySelector('button.roll_roulette').remove();
			root.querySelector('p.result_roll').remove();
			root.querySelector('p.result_roll_nothing').style.display = '';
		}else{
			root.querySelector('p.result_roll').style.display = '';
			root.querySelector('label.input').remove();
			root.querySelector('button.roll_roulette').remove();
			codematrixPopupTargetAction();
		}
	});
	<?=$arResult['BUTTON_METRIC']?>
}

var addSector = function(data, startAngle, collapse) {
  var sectorDeg = 3.6 * data.value;
  var skewDeg = 90 + sectorDeg;
  var rotateDeg = startAngle;
  if (collapse) {
    skewDeg++;
  }
  var container = document.querySelector('#codematrix_roulette .container');
  var sector = document.createElement('div');
  sector.style.background=data.color;
  sector.className ='sector';
  sector.style.transform='rotate(' + rotateDeg + 'deg) skewY(' + skewDeg + 'deg)';
  container.appendChild(sector);
  var container_text=document.querySelector('#codematrix_roulette .text');
  var text = document.createElement('div');
  text.style.color='white';
  text.dataset.rule=data.rule;
  text.textContent=data.text;
  text.style.transform='rotate('+((rotateDeg-(90-sectorDeg/2)))+'deg) translate(0px,-50%)';
  container_text.appendChild(text);
  return startAngle + sectorDeg;
};

function paintRoulett(){
	dataset.reduce(function (prev, curr) {
		return (function addPart(data, angle) {
			if (data.value <= maxValue) {
				return addSector(data, angle, false);
			}
			return addPart({
				value: data.value - maxValue,
				color: data.color
			}, addSector({
				value: maxValue,
				color: data.color,
			}, angle, true));
		})(curr, prev);
	}, 0);
	return dataset.length;
}
    </script>
    <p class="result_roll" style="display:none;"><?=htmlspecialcharsbx((string)($arResult['RESULT_TEXT'] ?? ''))?></p>
    <p class="result_roll_nothing" style="display:none;"><?=htmlspecialcharsbx((string)($arResult['NOTHING_TEXT'] ?? ''))?></p>
      <label class="email input">
        <input placeholder="<?=htmlspecialcharsbx((string)($arResult['EMAIL_PLACEHOLDER'] ?? ''))?>" type="email" name="email">
        <p class="result_roll_fail" style="display:none;"><?=GetMessage('codematrix.roulette_wrong')?><span></span></p>
        <p class="result_roll_not_unique" style="display:none;"><?=htmlspecialcharsbx((string)($arResult['EMAIL_NOT_NEW_TEXT'] ?? ''))?><span></span></p>
        <p class="result_roll_coupon_error" style="display:none;"><?=GetMessage("codematrix.roulette_coupon_error")?></p>
      </label>
      <button class="roll_roulette"><?=htmlspecialcharsbx((string)($arResult['BUTTON_TEXT'] ?? ''))?></button>
</div>
