<?
namespace CodeMatrix\Popup;
\Bitrix\Main\Loader::includeModule('iblock');
use Bitrix\Main\Mail\Event;
class Tools{

	public static function getBasketRules(){
		$currentModules=array();
		 $groupDiscountIterator = \Bitrix\Sale\Internals\DiscountTable::getList(array(
            'select' => array('ID', 'NAME'),
            'filter' => array('=ACTIVE' => 'Y')
        ));
        while ($groupDiscount = $groupDiscountIterator->fetch()) {
			$currentModules[$groupDiscount['ID']]=$groupDiscount['NAME'].' ['.$groupDiscount['ID'].']';
        }
		return $currentModules;
	}
	public static function getMailTemplates($var='CODEMATRIX_POPUP_SEND_COUPON'){
		$templates=Mail::getTemplates($var);
		$serviceMessage=array();
		foreach($templates as $template){
			$serviceMessage[$template['ID']]=$template['EVENT_NAME'].' ['.$template['ID'].']';
		}
		return $serviceMessage;
	}
	public static function returnPropId($iblock_id, $prop_code, $prop_name){
		\Bitrix\Main\Loader::includeModule('iblock');
		$properties = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$iblock_id, 'CODE'=>strtoupper($prop_code)));
		if($prop_fields = $properties->GetNext()){
			return $prop_fields["ID"];
		}
		$ibp = new \CIBlockProperty;
		return $ibp->Add(array(
			"NAME" => $prop_name,
			"ACTIVE" => "Y",
			"SORT" => "100",
			"CODE" => strtoupper($prop_code),
			"PROPERTY_TYPE" => "S",
			"IBLOCK_ID" => $iblock_id
		));
	}

	public static function getUserGroup(){
		$res = \CGroup::getList(($by="ID"),($order="asc"));
		$result=array();
		while($r=$res->Fetch()){
			$result[$r['ID']]=$r['NAME'];
		}
		return $result;
	}
}

?>