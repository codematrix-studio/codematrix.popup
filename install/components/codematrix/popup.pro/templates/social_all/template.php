<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
$vk = $arResult['ID_VK'];
$odnkl = $arResult['ID_ODNKL'];
$count = 0;
if(!empty($vk))
	$count++;
if(!empty($odnkl))
	$count++;
$count *= 300;
?>
<div id="codematrix_social_all" style="
background: <?=$arResult['COLOR_BG']?>;
background: -moz-linear-gradient(top,<?=$arResult['COLOR_BG']?> 0%, #fff 200%);
background: -webkit-linear-gradient(top, <?=$arResult['COLOR_BG']?> 0%,#fff 200%);
background: linear-gradient(to bottom,<?=$arResult['COLOR_BG']?> 0%,#fff 200%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?=$arResult['COLOR_BG']?>', endColorstr='#fff',GradientType=0 );
width:<?=$count?>px;
">
	<h2>
		<?=$arResult['TITLE']?>
	</h2>
	<div class="social_holder">
		<?php if(!empty($arResult['ID_VK'])){?>
		<!-- Виджет ВКонтакте. -->
		<div id="vk_groups"></div>
		<script type="text/javascript">
			var vk_js = document.createElement("script");
			document.head.appendChild(vk_js);
			vk_js.src = "https://vk.com/js/api/openapi.js?146";
			vk_js.onload = vk_js.onreadystatechange = function () {
				var loadVK=function(){
					var targetVK=document.getElementById('vk_groups');
					if(targetVK){
						targetVK.innerHTML='';
						VK.Widgets.Group("vk_groups", {mode: 5, width: "300",height:"316"}, <?=$arResult['ID_VK']?>);
					}else{
						setTimeout(loadVK, 20);
					}
				}
				loadVK();
			}
		</script>
		<?php }?>
		<?php if(!empty($arResult['ID_ODNKL'])){?>
		<div id="ok_group_widget"></div>
		<script>
		!function (d, id, did, st) {
		  var js = d.createElement("script");
		  js.src = "https://connect.ok.ru/connect.js";
		  js.onload = js.onreadystatechange = function () {
		  if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
			if (!this.executed) {
			  this.executed = true;
			  var loadOK=function(){
				var targetOK=document.getElementById(id);
				if(targetOK){
					targetOK.innerHTML='';
					OK.CONNECT.insertGroupWidget(id,did,st);
				}else{
					setTimeout(loadOK, 20);
				}
			  }
			  loadOK();
			}
		  }}
		  d.documentElement.appendChild(js);
		}(document,"ok_group_widget","<?=$arResult['ID_ODNKL']?>",'{"width":300,"height":316}');
		</script>
		<?php }?>
		<div class="clear"></div>
	</div>
</div>