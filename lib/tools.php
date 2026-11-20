<?php
namespace CodeMatrix\Popup;
\Bitrix\Main\Loader::includeModule('iblock');
use Bitrix\Main\Mail\Event;
class Tools{

	/**
	 * Возвращает активные правила скидок в формате «ID => название [ID]».
	 *
	 * @return array<int, string>
	 */
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
	/**
	 * Возвращает почтовые шаблоны указанного типа события для списка в настройках.
	 *
	 * @param string $var Код почтового события.
	 * @return array<int, string> Массив «ID шаблона => код события [ID]».
	 */
	public static function getMailTemplates($var='CODEMATRIX_POPUP_SEND_COUPON'){
		$templates=Mail::getTemplates($var);
		$serviceMessage=array();
		foreach($templates as $template){
			$serviceMessage[$template['ID']]=$template['EVENT_NAME'].' ['.$template['ID'].']';
		}
		return $serviceMessage;
	}
	/**
	 * Возвращает ID свойства инфоблока или создаёт строковое свойство, если оно отсутствует.
	 *
	 * @param int|string $iblock_id ID инфоблока.
	 * @param string $prop_code Символьный код свойства.
	 * @param string $prop_name Название создаваемого свойства.
	 * @return int|string|false ID свойства либо false при ошибке создания.
	 */
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

	/**
	 * Возвращает список групп пользователей в формате «ID => название».
	 *
	 * @return array<int, string>
	 */
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