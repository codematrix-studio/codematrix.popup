<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
$blocks = array();
if(!empty($arResult['ID_VK'])){
	$blocks[]='ID_VK';
}
if(!empty($arResult['ID_ODNKL'])){
	$blocks[]='ID_ODNKL';
}
$choosen = !empty($blocks) ? $blocks[array_rand($blocks)] : '';
$arResult['COLOR_BG']=(empty($arResult['COLOR_BG']))?'#fff':$arResult['COLOR_BG'];
?>
<div id="codematrix_banner_default" style="background:<?=$arResult['COLOR_BG']?>;">
	<div class="top_border"></div>
	<div class="left_border"></div>
	<h2><?=$arResult['TITLE']?></h2>
	<?php $img='vk.png';
		if($choosen == 'ID_VK'){
	?>
		<img src="<?=$templateFolder?>/img/<?=$img?>">
			<div id="codematrix_vk_groups"></div>
			<script type="text/javascript">
				var vk_js = document.createElement("script");
				document.head.appendChild(vk_js);
				vk_js.src = "https://vk.com/js/api/openapi.js?146";
				vkEl = document.getElementById('codematrix_vk_groups');
				if(vkEl){
					vkEl.innerHTML='';
				}
				vk_js.onload = vk_js.onreadystatechange = function () {
					VK.Widgets.Group('codematrix_vk_groups', {mode: 5, width:'auto', height:'316'}, '<?=$arResult['ID_VK']?>');
				}
			</script>
	<?php }elseif($choosen=='ID_ODNKL'){
		$img='odnkl.png';?>
		<img src="<?=$templateFolder?>/img/<?=$img?>">
		<div id="ok_group_widget"></div>
		<script>
		!function (d, id, did, st) {
		  var js = d.createElement("script");
		  js.src = "https://connect.ok.ru/connect.js";
		  js.onload = js.onreadystatechange = function () {
		  if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
			if (!this.executed) {
			  this.executed = true;
			  setTimeout(function () {
				OK.CONNECT.insertGroupWidget(id,did,st);
			  }, 0);
			}
		  }}
		  d.documentElement.appendChild(js);
		}(document,"ok_group_widget","<?=$arResult['ID_ODNKL']?>",'{"width":305,"height":316}');
		</script>
	<?php }?>
	<div class="right_border"></div>
	<div class="bottom_border"></div>
</div>