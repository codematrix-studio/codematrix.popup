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
		$iblockId = (int)$iblock_id;
		$propertyCode = strtoupper((string)$prop_code);
		$property = \Bitrix\Iblock\PropertyTable::getList(array(
			'select' => array('ID'),
			'filter' => array(
				'=IBLOCK_ID' => $iblockId,
				'=CODE' => $propertyCode
			),
			'order' => array('SORT' => 'ASC', 'NAME' => 'ASC'),
			'limit' => 1
		))->fetch();
		if($property){
			return $property['ID'];
		}
		$result = \Bitrix\Iblock\PropertyTable::add(array(
			'NAME' => (string)$prop_name,
			'ACTIVE' => 'Y',
			'SORT' => 100,
			'CODE' => $propertyCode,
			'PROPERTY_TYPE' => 'S',
			'IBLOCK_ID' => $iblockId
		));
		return $result->isSuccess() ? $result->getId() : false;
	}

	public static function getUserGroup(){
		$result = array();
		$groups = \Bitrix\Main\GroupTable::getList(array(
			'select' => array('ID', 'NAME'),
			'order' => array('ID' => 'ASC')
		));
		while($group = $groups->fetch()){
			$result[$group['ID']] = $group['NAME'];
		}
		return $result;
	}
}

?>