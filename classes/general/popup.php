<?
use \Bitrix\Main\Application,
	Bitrix\Main,
	Bitrix\Main\Web\Cookie,
	Bitrix\Main\Context,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Page\Asset,
	Bitrix\Main\UserConsent\Internals\AgreementTable,
	Bitrix\Main\UserConsent\Agreement;
use Bitrix\Main\Mail\Event;
use Bitrix\Sale\Internals;
\Bitrix\Main\Loader::IncludeModule('sale');
\Bitrix\Main\Loader::IncludeModule('catalog');
Loc::loadMessages(__FILE__);
class popup{

	protected $tableSetting;
	protected $tableColorThemes;
	protected $tableTemplates;				   
	protected $idPopup;
	protected $consentList;
	protected $site_id;
	const idModule='codematrix.popup';

	function __construct($id='new'){
		$this->tableSetting='codematrix_popup';
		$this->tableColorThemes='codematrix_popup_add_colors';
		$this->tableTemplates='codematrix_popup_add_templates';
		$this->idPopup=$id;
		$this->consentList='none';
		$this->site_id=SITE_ID;
	}

	public function getConsentList(){
		if($this->consentList=='none'){
			if (class_exists('Bitrix\Main\UserConsent\Agreement')){
				$tmpList=array();
				$list = AgreementTable::getList(array(
					'select' => array('ID', 'DATE_INSERT', 'ACTIVE', 'NAME', 'TYPE'),
					'filter' => array('ACTIVE' => 'Y'),
					'order' => array('ID' => 'ASC')
				));
				foreach($list as $item){
					$tmpList[$item['ID']]=$item['NAME'];
				}
				if(count($tmpList)>0){
					$this->consentList=$tmpList;
				}
			}
		}
		return ($this->consentList=='none')?array():$this->consentList;
	}

	public function getAgreements($agrArr=array()){
		$retArr=array();
		if (class_exists('Bitrix\Main\UserConsent\Agreement')){
			$agreements=new Agreement(1);
			$agreements=$agreements::getActiveList();
			if(count($agreements)>0){
				foreach($agreements as $key=>$agreement){
					$tmpAgreement=new Agreement($key, $agrArr);
					$retArr[$key] = $tmpAgreement->getLabelText();
				}
			}
		}
		return $retArr;
	}

	public function setPopupId($id){
		$this->idPopup=$id;
	}

	public function getTableSetting(){
		return $this->tableSetting;
	}

	private function getTypesPreset(){
		$type=array(
			/* 1. Изображение (баннер) */
			'banner'=>array(
				'code'=>'banner',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_BANNER"),
				'active'=>true,
				'props'=>array(
					'IMG_1_SRC'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_BANNER_CONTENT_IMG_1_SRC")),
					'LINK_HREF'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_BANNER_CONTENT_LINK_HREF")),
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),
					'HREF_TARGET'=>array(
							'type'=>'service',
							'tag'=>'select',
							'name'=>GetMessage("codematrix.popup_HREF_TARGET"),
							'list'=>array('_blank'=>GetMessage("codematrix.popup_HREF_TARGET_BLANK"), '_self'=>GetMessage("codematrix.popup_HREF_TARGET_SELF")),
						)
				)
			),

			/* 2. Видео */
			'video'=>array(
				'code'=>'video',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_VIDEO"),
				'props'=>array(
					'LINK_VIDEO'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_VIDEO_CONTENT_LINK_VIDEO")),
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),
					'VIDEO_SIMILAR'=>array(
						'type'=>'service',
						'tag'=>'select',
						'list'=>array(0=>GetMessage("codematrix.popup_NO"), 1=>GetMessage("codematrix.popup_YES")),
						'name'=>GetMessage("codematrix.popup_VIDEO_SERVICE_VIDEO_SIMILAR")
					),
					'VIDEO_AUTOPLAY'=>array(
						'type'=>'service',
						'name'=>GetMessage("codematrix.popup_VIDEO_SERVICE_VIDEO_AUTOPLAY"),
						'tag'=>'select',
						'list'=>array(0=>GetMessage("codematrix.popup_NO"), 1=>GetMessage("codematrix.popup_YES")),
					)
				)
			),

			/* 3. Акции */
			'action'=>array(
				'code'=>'action',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_ACTION"),
				'color_style'=>array(
					'green'=>GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
					'red'=>GetMessage("codematrix.popup_ACTION_COLOR_RED"),
					'blue'=>GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
					'wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA"),
					'orange'=>GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
					'pumpkin'=>GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
					'greensea'=>GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
					'midnightblue'=>GetMessage("codematrix.popup_ACTION_COLOR_MIDNIGHTBLUE"),
					'dark'=>GetMessage("codematrix.popup_ACTION_COLOR_DARK"),
					'asbestos'=>GetMessage("codematrix.popup_ACTION_COLOR_ASBESTOS")
				),
				'props'=>array(
					'TITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_ACTION_CONTENT_TITLE")),
					'IMG_1_SRC'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_ACTION_CONTENT_IMG_1_SRC")),
					'SUBTITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_ACTION_CONTENT_SUBTITLE")),
					'CONTENT'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_ACTION_CONTENT_CONTENT")),
					'LINK_TEXT'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_ACTION_CONTENT_LINK_TEXT")),
					'LINK_HREF'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_ACTION_CONTENT_LINK_HREF")),
					'BUTTON_METRIC'=>array('type'=>'content','tag'=>'textarea','name'=>GetMessage('codematrix.popup_BUTTON_METRIC'),'hint'=>GetMessage('codematrix.popup_BUTTON_METRIC_HINT')),
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),
					'HREF_TARGET'=>array(
							'type'=>'service',
							'tag'=>'select',
							'name'=>GetMessage("codematrix.popup_HREF_TARGET"),
							'list'=>array('_blank'=>GetMessage("codematrix.popup_HREF_TARGET_BLANK"), '_self'=>GetMessage("codematrix.popup_HREF_TARGET_SELF")),
						)
				)
			),

			/* 4. Социальные сети */
			'social'=>array(
				'code'=>'social',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_SOCIAL"),
				'props'=>array(
					'TITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_VIDEO_CONTENT_TITLE")), /* Удалить */
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),
					'COLOR_BG'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_SOCIAL_SERVICE_COLOR_BG")),
					'ID_VK'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_SOCIAL_SERVICE_ID_VK")),
					/* 'ID_FB'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_SOCIAL_SERVICE_ID_FB")), */
					'ID_INST'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_SOCIAL_SERVICE_ID_INST")),
					'ID_ODNKL'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_SOCIAL_SERVICE_ID_ODNKL")),
					/* 'TYPE_VIEW'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_SOCIAL_SERVICE_TYPE_VIEW")), */
					'HREF_TARGET'=>array(
							'type'=>'service',
							'tag'=>'select',
							'name'=>GetMessage("codematrix.popup_HREF_TARGET"),
							'list'=>array('_blank'=>GetMessage("codematrix.popup_HREF_TARGET_BLANK"), '_self'=>GetMessage("codematrix.popup_HREF_TARGET_SELF")),
						)
				)
			),

			/* 5. Сбор контактов */
			'contact'=>array(
				'code'=>'contact',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_CONTACT"),
				'color_style'=>array(
					'grad_blue-wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_BLUE-WISTERIA"),
					'grad_green-blue'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_GREEN-BLUE"),
					'grad_greensea-blue'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_GREENSEA-BLUE"),
					'grad_greensea-green'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_GREENSEA-GREEN"),
					'grad_red-orange'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_RED-ORANGE"),
					'grad_wisteria-red'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_WISTERIA-RED"),
					'blue'=>GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
					'green'=>GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
					'greensea'=>GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
					'orange'=>GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
					'pumpkin'=>GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
					'red'=>GetMessage("codematrix.popup_ACTION_COLOR_RED"),
					'wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA")
				),
				'props'=>array(
					'IMG_1_SRC'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_CONTACT_CONTENT_MAIN_IMG")),
					'TITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_CONTACT_CONTENT_TITLE")),
					'SUBTITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_CONTACT_CONTENT_SUBTITLE")),
					'BUTTON_TEXT'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_CONTACT_CONTENT_BUTTON_TEXT")),
					'BUTTON_METRIC'=>array('type'=>'content','tag'=>'textarea','name'=>GetMessage('codematrix.popup_BUTTON_METRIC'),'hint'=>GetMessage('codematrix.popup_BUTTON_METRIC_HINT')),
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),

					'EMAIL_SHOW'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_EMAIL_SHOW"),'block'=>'start'),
					'EMAIL_REQUIRED'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_EMAIL_REQUIRED")),
					'EMAIL_TITLE'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_TITLE")),
					'EMAIL_PLACEHOLDER'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_PLACEHOLDER"),'block'=>'end'),

					'NAME_SHOW'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_NAME_SHOW"),'block'=>'start'),
					'NAME_REQUIRED'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_NAME_REQUIRED")),
					'NAME_TITLE'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_TITLE")),
					'NAME_PLACEHOLDER'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_PLACEHOLDER"),'block'=>'end'),

					'PHONE_SHOW'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_PHONE_SHOW"),'block'=>'start'),
					'PHONE_REQUIRED'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_PHONE_REQUIRED")),
					'PHONE_TITLE'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_TITLE")),
					'PHONE_PLACEHOLDER'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_PLACEHOLDER"),'block'=>'end'),

					'DESCRIPTION_SHOW'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_SHOW"),'block'=>'start'),
					'DESCRIPTION_REQUIRED'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_REQUIRED")),
					'DESCRIPTION_TITLE'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_TITLE")),
					'DESCRIPTION_PLACEHOLDER'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_PLACEHOLDER"),'block'=>'end'),

					'USE_CONSENT_SHOW'=>array('type'=>'service',  'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_CONSENT"),'block'=>'start'),
					'CONSENT_LIST'=>array('type'=>'service',  'tag'=>'select', 'name'=>GetMessage("codematrix.popup_CONTACT_CONSENT_LIST"), 'list'=>$this->getConsentList(), 'block'=>'end'),

					'HREF_TARGET'=>array(
							'type'=>'service',
							'tag'=>'select',
							'name'=>GetMessage("codematrix.popup_HREF_TARGET"),
							'list'=>array('_blank'=>GetMessage("codematrix.popup_HREF_TARGET_BLANK"), '_self'=>GetMessage("codematrix.popup_HREF_TARGET_SELF")),
						)
				)
			),


			'share'=>array(
				'code'=>'share',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_SHARE"),
				'props'=>array(
					'TITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_SHARE_CONTENT_TITLE")),
					'SUBTITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_SHARE_CONTENT_SUBTITLE")),
					'SOC_VK'=>array('type'=>'content','tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_SHARE_CONTENT_VK")),
					'SOC_FB'=>array('type'=>'content','tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_SHARE_SERVICE_FB")),
					'SOC_OD'=>array('type'=>'content','tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_SHARE_SERVICE_OD")),
					'SOC_TW'=>array('type'=>'content','tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_SHARE_SERVICE_TW")),
					'SOC_GP'=>array('type'=>'content','tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_SHARE_SERVICE_GP")),
					'SOC_MR'=>array('type'=>'content','tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_SHARE_SERVICE_MR")),
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),
					'HREF_TARGET'=>array(
							'type'=>'service',
							'tag'=>'select',
							'name'=>GetMessage("codematrix.popup_HREF_TARGET"),
							'list'=>array('_blank'=>GetMessage("codematrix.popup_HREF_TARGET_BLANK"), '_self'=>GetMessage("codematrix.popup_HREF_TARGET_SELF")),
						)
				)
			),

			/* 7. HTML */
			'html'=>array(
				'code'=>'html',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_HTML"),
				'props'=>array(
					//'TITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_SHARE_CONTENT_TITLE")),
					'TEXTAREA'=>array('type'=>'content', 'tag'=>'textarea', 'name'=>GetMessage("codematrix.popup_HTML_CONTENT_TEXTAREA"),'row'=>'10'),
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),
					'HREF_TARGET'=>array(
							'type'=>'service',
							'tag'=>'select',
							'name'=>GetMessage("codematrix.popup_HREF_TARGET"),
							'list'=>array('_blank'=>GetMessage("codematrix.popup_HREF_TARGET_BLANK"), '_self'=>GetMessage("codematrix.popup_HREF_TARGET_SELF")),
						)
				)
			),
			/* 8. Окно 18+ */
			'age'=>array(
				'code'=>'age',
				'name'=>GetMessage('codematrix.popup_TYPE_NAME_AGE'),
				'props'=>array(
					'TITLE'=>array('type'=>'content','name'=>GetMessage('codematrix.popup_AGE_CONTENT_TITLE')),
					'BUTTON_TEXT_Y'=>array('type'=>'content','name'=>GetMessage('codematrix.popup_AGE_CONTENT_BUTTON_Y')),
					'BUTTON_TEXT_N'=>array('type'=>'content','name'=>GetMessage('codematrix.popup_AGE_CONTENT_BUTTON_N')),
					'IMG_1_SRC'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_AGE_CONTENT_MAIN_IMG")),
					'HREF_LINK'=>array('type'=>'content','name'=>GetMessage('codematrix.popup_AGE_CONTENT_HREF_LINK')),
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),
				)
			)
		);
		if(\Bitrix\Main\Loader::IncludeModule('sale')){
			$type['coupon']=array(
				'code'=>'coupon',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_COUPON"),
				'color_style'=>array(
					'green'=>GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
					'red'=>GetMessage("codematrix.popup_ACTION_COLOR_RED"),
					'blue'=>GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
					'wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA"),
					'orange'=>GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
					'pumpkin'=>GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
					'greensea'=>GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
					'midnightblue'=>GetMessage("codematrix.popup_ACTION_COLOR_MIDNIGHTBLUE"),
					'dark'=>GetMessage("codematrix.popup_ACTION_COLOR_DARK"),
					'asbestos'=>GetMessage("codematrix.popup_ACTION_COLOR_ASBESTOS")
				),
				'props'=>array(
					'TITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_COUPON_CONTENT_TITLE")),
					'SUBTITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_COUPON_CONTENT_SUBTITLE")),
					'IMG_1_SRC'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_COUPON_CONTENT_MAIN_IMG")),
					'BUTTON_TEXT'=>array('type'=>'content','name'=>GetMessage("codematrix.popup_COUPON_CONTENT_BUTTON_TEXT")),
					'BUTTON_METRIC'=>array('type'=>'content','tag'=>'textarea','name'=>GetMessage('codematrix.popup_BUTTON_METRIC'),'hint'=>GetMessage('codematrix.popup_BUTTON_METRIC_HINT')),
					'RULE_ID'=>array(
						'type'=>'service',
						'tag'=>'select',
						'list'=>CodeMatrix\Popup\Tools::getBasketRules(),
						'name'=>GetMessage("codematrix.popup_COUPON_CONTENT_MAIN_RULE_ID"),
						'hint'=>GetMessage('codematrix.popup_COUPON_CONTENT_MAIN_RULE_ID_HINT')
					),
					'TIMING'=>array('type'=>'service','name'=>GetMessage("codematrix.popup_COUPON_CONTENT_MAIN_TIMING"),'hint'=>GetMessage('codematrix.popup_COUPON_CONTENT_MAIN_TIMING_HINT')),
					'EMAIL_SHOW'=>array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_EMAIL_SHOW"),'block'=>'start'),
					'EMAIL_PLACEHOLDER'=>array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_PLACEHOLDER")),
					'EMAIL_ADD2BASE'=>array('type'=>'service','tag'=>'checkbox','name'=>GetMessage('codematrix.popup_CONTACT_ADD'),'hint'=>GetMessage('codematrix.popup_CONTACT_ADD_HINT')),
					'EMAIL_EMAIL_TO'=>array('type'=>'service','tag'=>'checkbox','name'=>GetMessage('codematrix.popup_CONTACT_EMAIL_TO'),'hint'=>GetMessage('codematrix.popup_CONTACT_EMAIL_TO_HINT')),
					'EMAIL_TEMPLATE'=>array(
						'type'=>'service',
						'tag'=>'select',
						'name'=>GetMessage('codematrix.popup_CONTACT_TEMPLATE'),
						'hint'=>GetMessage('codematrix.popup_CONTACT_TEMPLATE_HINT'),
						'list'=>CodeMatrix\Popup\Tools::getMailTemplates()
					),
					'EMAIL_NOT_NEW'=>array('type'=>'service','tag'=>'checkbox','name'=>GetMessage('codematrix.popup_CONTACT_UNIQUE'),'hint'=>GetMessage('codematrix.popup_CONTACT_UNIQUE_HINT')),
					'EMAIL_NOT_NEW_TEXT'=>array('type'=>'service','name'=>GetMessage('codematrix.popup_COUPON_CONTENT_EMAIL_NOT_NEW'),'block'=>'end','hint'=>GetMessage('codematrix.popup_COUPON_CONTENT_EMAIL_NOT_NEW_HINT')),
					
					'POSITION_LEFT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT")),
					'POSITION_RIGHT'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT")),
					'POSITION_TOP'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP")),
					'POSITION_BOTTOM'=>array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM")),
				)
			);
		}
		$type['roulette']=array(
				'code'=>'roulette',
				'name'=>GetMessage("codematrix.popup_TYPE_NAME_ROULETTE"),
				'color_style'=>array(
					'green'=>GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
					'red'=>GetMessage("codematrix.popup_ACTION_COLOR_RED"),
					'blue'=>GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
					'wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA"),
					'orange'=>GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
					'pumpkin'=>GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
					'greensea'=>GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
				),
				'props'=>array(
					'TITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_ROULETTE_CONTENT_TITLE")),
					'SUBTITLE'=>array('type'=>'content', 'name'=>GetMessage("codematrix.popup_ROULETTE_CONTENT_SUBTITLE")),
					'BUTTON_TEXT'=>array('type'=>'content','name'=>GetMessage("codematrix.popup_ROULETTE_CONTENT_BUTTON_TEXT")),
					'RESULT_TEXT'=>array('type'=>'content','name'=>GetMessage("codematrix.popup_ROULETTE_RESULT_TITLE"),'hint'=>GetMessage('codematrix.popup_ROULETTE_RESULT_HINT')),
					'NOTHING_TEXT'=>array('type'=>'content','name'=>GetMessage("codematrix.popup_ROULETTE_NOTHING_TITLE"),'hint'=>GetMessage('codematrix.popup_ROULETTE_NOTHING_HINT')),
					'BUTTON_METRIC'=>array('type'=>'content','tag'=>'textarea','name'=>GetMessage('codematrix.popup_BUTTON_METRIC'),'hint'=>GetMessage('codematrix.popup_BUTTON_METRIC_HINT')),
				)
			);
		if(\Bitrix\Main\Loader::IncludeModule('sale')) {
            $type['roulette']['props']['TIMING']=array('type'=>'service','name'=>GetMessage("codematrix.popup_COUPON_CONTENT_MAIN_TIMING"),'hint'=>GetMessage('codematrix.popup_COUPON_CONTENT_MAIN_TIMING_HINT'));
        }

		$type['roulette']['props']['MAIL_TEMPLATE']=array(
						'type'=>'service',
						'tag'=>'select',
						'name'=>GetMessage('codematrix.popup_ROULETTE_TEMPLATE'),
						'hint'=>GetMessage('codematrix.popup_ROULETTE_TEMPLATE_HINT'),
						'list'=>CodeMatrix\Popup\Tools::getMailTemplates('CODEMATRIX_POPUP_ROULETTE_SEND')
					);
		$type['roulette']['props']['EMAIL_SHOW']=array('type'=>'service', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_CONTACT_EMAIL_SHOW"),'block'=>'start');
		$type['roulette']['props']['EMAIL_PLACEHOLDER']=array('type'=>'service', 'name'=>GetMessage("codematrix.popup_CONTACT_PLACEHOLDER"));
		$type['roulette']['props']['EMAIL_ADD2BASE']=array('type'=>'service','tag'=>'checkbox','name'=>GetMessage('codematrix.popup_CONTACT_ADD'),'hint'=>GetMessage('codematrix.popup_CONTACT_ADD_HINT'));
					
		$type['roulette']['props']['EMAIL_NOT_NEW']=array('type'=>'service','tag'=>'checkbox','name'=>GetMessage('codematrix.popup_CONTACT_UNIQUE'),'hint'=>GetMessage('codematrix.popup_CONTACT_UNIQUE_HINT'));
		$type['roulette']['props']['EMAIL_NOT_NEW_TEXT']=array('type'=>'service','name'=>GetMessage('codematrix.popup_ROULETTE_CONTENT_EMAIL_NOT_NEW'),'block'=>'end','hint'=>GetMessage('codematrix.popup_ROULETTE_CONTENT_EMAIL_NOT_NEW_HINT'));
		$type['roulette']['props']['POSITION_LEFT']=array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_LEFT"));
		$type['roulette']['props']['POSITION_RIGHT']=array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_RIGHT"));
		$type['roulette']['props']['POSITION_TOP']=array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_TOP"));
		$type['roulette']['props']['POSITION_BOTTOM']=array('type'=>'positionpopup', 'tag'=>'checkbox', 'name'=>GetMessage("codematrix.popup_POSITION_BOTTOM"));
        if (\Bitrix\Main\Loader::IncludeModule('sale')) {
            $type['discount'] = array(
                'code' => 'discount',
                'name' => GetMessage("codematrix.popup_TYPE_NAME_DISCOUNT"),
                'color_style' => array(
                    'green' => GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
                    'red' => GetMessage("codematrix.popup_ACTION_COLOR_RED"),
                    'blue' => GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
                    'wisteria' => GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA"),
                    'orange' => GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
                    'pumpkin' => GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
                    'greensea' => GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
                ),
                'props' => array(
                    'TITLE' => array('type' => 'content', 'name' => GetMessage("codematrix.popup_ROULETTE_CONTENT_TITLE")),
                    'SUBTITLE' => array('type' => 'content', 'name' => GetMessage("codematrix.popup_ROULETTE_CONTENT_SUBTITLE")),
                    'IMG_1_SRC' => array('type' => 'content', 'name' => GetMessage("codematrix.popup_DISCOUNT_IMG_1")),
                    'IMG_2_SRC' => array('type' => 'content', 'name' => GetMessage("codematrix.popup_DISCOUNT_IMG_2")),
                    'BUTTON_TEXT' => array('type' => 'content', 'name' => GetMessage("codematrix.popup_ROULETTE_CONTENT_BUTTON_TEXT")),
                    'BUTTON_METRIC' => array('type' => 'content', 'tag' => 'textarea', 'name' => GetMessage('codematrix.popup_BUTTON_METRIC'), 'hint' => GetMessage('codematrix.popup_BUTTON_METRIC_HINT')),
                    'RULE_ID' => array(
                        'type' => 'content',
                        'tag' => 'select',
                        'list' => CodeMatrix\Popup\Tools::getBasketRules(),
                        'name' => GetMessage("codematrix.popup_COUPON_CONTENT_MAIN_RULE_ID"),
                        'hint' => GetMessage('codematrix.popup_COUPON_CONTENT_MAIN_RULE_ID_HINT')
                    ),
                    'DISCOUNT_MASK' => array('type' => 'content', 'name' => GetMessage('codematrix.popup_DISCOUNT_MASK_TITLE'), 'hint' => GetMessage('codematrix.popup_DISCOUNT_MASK_TITLE_HINT')),
                    'USER_GROUP' => array(
                        'type' => 'content',
                        'tag' => 'select',
                        'list' => CodeMatrix\Popup\Tools::getUserGroup(),
                        'name' => GetMessage('codematrix.popup_DISCOUNT_USERGROUP_TITLE'),
                        'hint' => GetMessage('codematrix.popup_DISCOUNT_USERGROUP_TITLE_HINT')
                    ),


                    'NAME_SHOW' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_CONTACT_NAME_SHOW"), 'block' => 'start'),
                    'NAME_REQUIRED' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_CONTACT_NAME_REQUIRED")),
                    'NAME_TITLE' => array('type' => 'service', 'name' => GetMessage("codematrix.popup_CONTACT_TITLE"), 'block' => 'end'),


                    'LASTNAME_SHOW' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_DISCOUNT_LASTNAME_SHOW"), 'block' => 'start'),
                    'LASTNAME_REQUIRED' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_DISCOUNT_LASTNAME_REQUIRED")),
                    'LASTNAME_TITLE' => array('type' => 'service', 'name' => GetMessage("codematrix.popup_CONTACT_TITLE"), 'block' => 'end'),


                    'PHONE_SHOW' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_CONTACT_PHONE_SHOW"), 'block' => 'start'),
                    'PHONE_REQUIRED' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_CONTACT_PHONE_REQUIRED")),
                    'PHONE_TITLE' => array('type' => 'service', 'name' => GetMessage("codematrix.popup_CONTACT_TITLE"), 'block' => 'end'),


                    'EMAIL_SHOW' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_CONTACT_EMAIL_SHOW"), 'block' => 'start'),
                    'EMAIL_TITLE' => array('type' => 'service', 'name' => GetMessage("codematrix.popup_CONTACT_TITLE")),
                    'EMAIL_ADD2BASE' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage('codematrix.popup_CONTACT_ADD'), 'hint' => GetMessage('codematrix.popup_CONTACT_ADD_HINT')),
                    'EMAIL_TEMPLATE_D' => array(
                        'type' => 'service',
                        'tag' => 'select',
                        'name' => GetMessage('codematrix.popup_ROULETTE_TEMPLATE'),
                        'hint' => GetMessage('codematrix.popup_ROULETTE_TEMPLATE_HINT'),
                        'list' => CodeMatrix\Popup\Tools::getMailTemplates('CODEMATRIX_POPUP_DISCOUNT_SEND')
                    ),
                    'EMAIL_NOT_NEW' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage('codematrix.popup_CONTACT_UNIQUE'), 'hint' => GetMessage('codematrix.popup_CONTACT_UNIQUE_HINT')),
                    'EMAIL_NOT_NEW_TEXT' => array('type' => 'service', 'name' => GetMessage('codematrix.popup_ROULETTE_CONTENT_EMAIL_NOT_NEW'), 'block' => 'end', 'hint' => GetMessage('codematrix.popup_ROULETTE_CONTENT_EMAIL_NOT_NEW_HINT')),

                    'USE_CONSENT_SHOW' => array('type' => 'service', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_CONTACT_CONSENT"), 'block' => 'start'),
                    'CONSENT_LIST' => array('type' => 'service', 'tag' => 'select', 'name' => GetMessage("codematrix.popup_CONTACT_CONSENT_LIST"), 'list' => $this->getConsentList(), 'block' => 'end'),

                    'POSITION_LEFT' => array('type' => 'positionpopup', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_POSITION_LEFT")),
                    'POSITION_RIGHT' => array('type' => 'positionpopup', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_POSITION_RIGHT")),
                    'POSITION_TOP' => array('type' => 'positionpopup', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_POSITION_TOP")),
                    'POSITION_BOTTOM' => array('type' => 'positionpopup', 'tag' => 'checkbox', 'name' => GetMessage("codematrix.popup_POSITION_BOTTOM")),
                )
            );
        }
		
		return $type;
	}

	public function getTypes(){
		$type=$this->getTypesPreset();
		if($this->idPopup!='new'){
			$settings=$this->getSetting($this->idPopup);
			foreach($type as $keyType=>&$nextType){
				if($keyType==$settings['view']['type']){
					$nextType['active']=true;
				}else{
					$nextType['active']=false;
				}
			}
		}
		return $type;
	}

	protected function getTemplatesPreset(){
		$templates=array(
			/* 1. Изображение (баннер) */
			'banner'=>array(
				array(
					'template'=>'default',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_BANNER_T1"),
					'active'=>true,
					'props'=>array(
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/banner_1.jpg',
						'LINK_HREF'=>"https://codematrix.ru",
						'HREF_TARGET'=>'_blank',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				)
			),

			/* 2. Видео */
			'video'=>array(
				array(
					'template'=>'youtube',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_VIDEO_T1"),
					'active'=>true,
					'props'=>array(
						'LINK_VIDEO'=>'EHQqQENOEps',
						'VIDEO_SIMILAR'=>'0',
						'VIDEO_AUTOPLAY'=>'0',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				)
			),

			/* 3. Акции */
			'action'=>array(
				array(
					'template'=>'leftimg',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_ACTION_T1"),
					'active'=>true,
					'color_style'=>'green',
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_ACTION_TITLE"),
						'SUBTITLE'=>GetMessage("codematrix.popup_ACTION_SUBTITLE"),
						'CONTENT'=>GetMessage("codematrix.popup_ACTION_CONTENT"),
						'LINK_TEXT'=>GetMessage("codematrix.popup_ACTION_LINK_TEXT"),
						'LINK_HREF'=>'https://codematrix.ru',
						'BUTTON_METRIC'=>'',
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/gift_1.jpg',
						'HREF_TARGET'=>'_blank',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				),
				array(
					'template'=>'rightimg',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_ACTION_T2"),
					'color_style'=>'red',
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_ACTION_TITLE"),
						'SUBTITLE'=>GetMessage("codematrix.popup_ACTION_SUBTITLE"),
						'CONTENT'=>GetMessage("codematrix.popup_ACTION_CONTENT"),
						'LINK_TEXT'=>GetMessage("codematrix.popup_ACTION_LINK_TEXT"),
						'LINK_HREF'=>'https://codematrix.ru',
						'BUTTON_METRIC'=>'',
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/gift_1.jpg',
						'HREF_TARGET'=>'_blank',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				),
				array(
					'template'=>'top',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_ACTION_T3"),
					'color_style'=>'dark',
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_ACTION_TITLE"),
						'SUBTITLE'=>GetMessage("codematrix.popup_ACTION_SUBTITLE"),
						'CONTENT'=>GetMessage("codematrix.popup_ACTION_CONTENT"),
						'LINK_TEXT'=>GetMessage("codematrix.popup_ACTION_LINK_TEXT"),
						'LINK_HREF'=>'https://codematrix.ru',
						'BUTTON_METRIC'=>'',
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/gift_1.jpg',
						'HREF_TARGET'=>'_blank',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				)
			),

			/* 4. Социальные сети */
			'social'=>array(
				array(
					'template'=>'one',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_SOCIAL_T1"),
					'active'=>true,
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_SOCIAL_TITLE"),
						'COLOR_BG'=>'',
						'ID_VK'=>'89371159',
						'ID_INST'=>'cats_funny_inst',
						'ID_ODNKL'=>'50582132228315',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				),
				array(
					'template'=>'all',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_SOCIAL_T2"),
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_SOCIAL_TITLE"),
						'COLOR_BG'=>'#1E5799',
						'ID_VK'=>'89371159',
						'ID_INST'=>'cats_funny_inst',
						'ID_ODNKL'=>'50582132228315',
						'TYPE_VIEW'=>'123',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				)
			),

			/* 5. Сбор контактов */
			'contact'=>array(
				array(
					'template'=>'type1',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_CONTACT_T1"),
					'active'=>true,
					'color_style'=>'green',
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_CONTACT_TITLE"),
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/black_friday.png',
						'SUBTITLE'=>GetMessage("codematrix.popup_CONTACT_SUBTITLE"),
						'BUTTON_TEXT'=>GetMessage("codematrix.popup_CONTACT_SEND_BUTTON"),
						'BUTTON_METRIC'=>'',
						'EMAIL_SHOW'=>'Y',
						'EMAIL_REQUIRED'=>'Y',
						'EMAIL_TITLE'=>'email',
						'EMAIL_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_EMAIL_PLACEHOLDER"),

						'NAME_SHOW'=>'Y',
						'NAME_REQUIRED'=>'Y',
						'NAME_TITLE'=>GetMessage("codematrix.popup_CONTACT_NAME_TITLE"),
						'NAME_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_NAME_PLACEHOLDER"),

						'PHONE_SHOW'=>'Y',
						'PHONE_REQUIRED'=>'Y',
						'PHONE_TITLE'=>GetMessage("codematrix.popup_CONTACT_PHONE_TITLE"),
						'PHONE_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_PHONE_PLACEHOLDER"),

						'DESCRIPTION_SHOW'=>'Y',
						'DESCRIPTION_REQUIRED'=>'Y',
						'DESCRIPTION_TITLE'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_TITLE"),
						'DESCRIPTION_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_PLACEHOLDER"),

						'USE_CONSENT_SHOW'=>'Y',
						'CONSENT_LIST'=>'1',

						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				),
				array(
					'template'=>'type2',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_CONTACT_T2"),
					'color_style'=>'grad_blue-wisteria',
					'color_styles'=>array(
						'grad_blue-wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_BLUE-WISTERIA"),
						'grad_green-blue'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_GREEN-BLUE"),
						'grad_greensea-blue'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_GREENSEA-BLUE"),
						'grad_greensea-green'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_GREENSEA-GREEN"),
						'grad_red-orange'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_RED-ORANGE"),
						'grad_wisteria-red'=>GetMessage("codematrix.popup_ACTION_COLOR_GRAD_WISTERIA-RED")
					),
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_CONTACT_TITLE2"),
						/*'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/contact_2.jpg',*/
						'SUBTITLE'=>GetMessage("codematrix.popup_CONTACT_SUBTITLE2"),
						'BUTTON_TEXT'=>GetMessage("codematrix.popup_CONTACT_SEND_BUTTON2"),
						'BUTTON_METRIC'=>'',
						'EMAIL_SHOW'=>'Y',
						'EMAIL_REQUIRED'=>'Y',
						'EMAIL_TITLE'=>'email',
						'EMAIL_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_EMAIL_PLACEHOLDER"),

						'NAME_SHOW'=>'Y',
						'NAME_REQUIRED'=>'Y',
						'NAME_TITLE'=>GetMessage("codematrix.popup_CONTACT_NAME_TITLE"),
						'NAME_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_NAME_PLACEHOLDER"),

						'PHONE_SHOW'=>'Y',
						'PHONE_REQUIRED'=>'Y',
						'PHONE_TITLE'=>GetMessage("codematrix.popup_CONTACT_PHONE_TITLE"),
						'PHONE_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_PHONE_PLACEHOLDER"),

						'DESCRIPTION_SHOW'=>'Y',
						'DESCRIPTION_REQUIRED'=>'Y',
						'DESCRIPTION_TITLE'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_TITLE"),
						'DESCRIPTION_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_PLACEHOLDER"),

						'USE_CONSENT_SHOW'=>'Y',
						'CONSENT_LIST'=>'1',

						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				),
				array(
					'template'=>'type3',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_CONTACT_T3"),
					'color_style'=>'green',
					'color_styles'=>array(
						'blue'=>GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
						'green'=>GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
						'greensea'=>GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
						'orange'=>GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
						'pumpkin'=>GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
						'red'=>GetMessage("codematrix.popup_ACTION_COLOR_RED"),
						'wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA")
					),
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_CONTACT_TITLE3"),
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/bisnesplan.png',
						'SUBTITLE'=>GetMessage("codematrix.popup_CONTACT_SUBTITLE3"),
						'BUTTON_TEXT'=>GetMessage("codematrix.popup_CONTACT_SEND_BUTTON3"),
						'BUTTON_METRIC'=>'',
						'EMAIL_SHOW'=>'Y',
						'EMAIL_REQUIRED'=>'Y',
						'EMAIL_TITLE'=>'email',
						'EMAIL_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_EMAIL_PLACEHOLDER"),

						'NAME_SHOW'=>'Y',
						'NAME_REQUIRED'=>'Y',
						'NAME_TITLE'=>GetMessage("codematrix.popup_CONTACT_NAME_TITLE"),
						'NAME_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_NAME_PLACEHOLDER"),

						'PHONE_SHOW'=>'Y',
						'PHONE_REQUIRED'=>'Y',
						'PHONE_TITLE'=>GetMessage("codematrix.popup_CONTACT_PHONE_TITLE"),
						'PHONE_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_PHONE_PLACEHOLDER"),

						'DESCRIPTION_SHOW'=>'Y',
						'DESCRIPTION_REQUIRED'=>'Y',
						'DESCRIPTION_TITLE'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_TITLE3"),
						'DESCRIPTION_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_PLACEHOLDER3"),

						'USE_CONSENT_SHOW'=>'Y',
						'CONSENT_LIST'=>'1',

						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				),
				array(
					'template'=>'type4',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_CONTACT_T4"),
					'color_style'=>'blue',
					'color_styles'=>array(
						'blue'=>GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
						'green'=>GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
						'greensea'=>GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
						'orange'=>GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
						'pumpkin'=>GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
						'red'=>GetMessage("codematrix.popup_ACTION_COLOR_RED"),
						'wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA")
					),
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_CONTACT_TITLE4"),
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/cotntact_type4.jpg',
						'SUBTITLE'=>GetMessage("codematrix.popup_CONTACT_SUBTITLE4"),
						'BUTTON_TEXT'=>GetMessage("codematrix.popup_CONTACT_SEND_BUTTON4"),
						'BUTTON_METRIC'=>'',
						'EMAIL_SHOW'=>'Y',
						'EMAIL_REQUIRED'=>'Y',
						'EMAIL_TITLE'=>'email',
						'EMAIL_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_EMAIL_PLACEHOLDER"),

						'NAME_SHOW'=>'Y',
						'NAME_REQUIRED'=>'Y',
						'NAME_TITLE'=>GetMessage("codematrix.popup_CONTACT_NAME_TITLE"),
						'NAME_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_NAME_PLACEHOLDER"),

						'PHONE_SHOW'=>'Y',
						'PHONE_REQUIRED'=>'Y',
						'PHONE_TITLE'=>GetMessage("codematrix.popup_CONTACT_PHONE_TITLE"),
						'PHONE_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_PHONE_PLACEHOLDER"),

						'DESCRIPTION_SHOW'=>'Y',
						'DESCRIPTION_REQUIRED'=>'Y',
						'DESCRIPTION_TITLE'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_TITLE4"),
						'DESCRIPTION_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_DESCRIPTION_PLACEHOLDER3"),

						'USE_CONSENT_SHOW'=>'Y',
						'CONSENT_LIST'=>'1',

						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				)

			),

			/* 6. Поделиться в социальной сети */
			'share'=>array(
				array(
					'template'=>'default',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_SHARE_T1"),
					'active'=>true,
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_SHARE_CONTENT_TITLE"),
						'SUBTITLE'=>GetMessage("codematrix.popup_SHARE_CONTENT_SUBTITLE"),
						'SOC_VK'=>'Y',
						'SOC_FB'=>'Y',
						'SOC_OD'=>'Y',
						'SOC_TW'=>'Y',
						'SOC_GP'=>'Y',
						'SOC_MR'=>'Y',
						'HREF_TARGET'=>'_blank',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				)
			),

			/* 7. HTML */
			'html'=>array(
				array(
					'template'=>'default',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_HTML_T1"),
					'active'=>true,
					'props'=>array(
						'TEXTAREA'=>'<div style="text-align:center; padding:10px; background:#16a085"><h1 style="text-align:center; padding:10px; margin:0; background:#e67e22">'.GetMessage("codematrix.popup_TYPE_NAME_HTML_SOMECODE").'</h1></div>',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				)
			),
			/* 8. Окно 18+ */
			'age'=>array(
				array(
					'template'=>'default',
					'name'=>GetMessage('codematrix.popup_TYPE_NAME_AGE_T1'),
					'active'=>true,
					'props'=>array(
						'TITLE'=>GetMessage('codematrix.popup_AGE_CONTENT_TITLE_DEF'),
						'BUTTON_TEXT_Y'=>GetMessage('codematrix.popup_AGE_CONTENT_BUTTON_Y_DEF'),
						'BUTTON_TEXT_N'=>GetMessage('codematrix.popup_AGE_CONTENT_BUTTON_N_DEF'),
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/age.png',
						'HREF_LINK'=>'http://disney.ru/',
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
					)
				)
			)
		);
		if (\Bitrix\Main\Loader::IncludeModule('sale')){
			/* 9. Купон на скидку */
			$serviceMessage=\CodeMatrix\Popup\Mail::getTemplates('CODEMATRIX_POPUP_SEND_COUPON');
			 $templates['coupon']=array(
				array(
					'template'=>'type1',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_COUPON_T1"),
					'active'=>true,
					'color_style'=>'blue',
					'color_styles'=>array(
						'blue'=>GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
						'green'=>GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
						'greensea'=>GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
						'orange'=>GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
						'pumpkin'=>GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
						'red'=>GetMessage("codematrix.popup_ACTION_COLOR_RED"),
						'wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA")
					),
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_COUPON_CONTENT_TITLE_DEFAULT"),
						'SUBTITLE'=>GetMessage("codematrix.popup_COUPON_CONTENT_SUBTITLE_DEFAULT"),
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/coupon_1.jpg',
						'BUTTON_TEXT'=>GetMessage("codematrix.popup_COUPON_CONTENT_BUTTON_TEXT_DEFAULT"),
						'BUTTON_METRIC'=>'',
						'RULE_ID'=>GetMessage("codematrix.popup_COUPON_CONTENT_MAIN_RULE_ID_DEFAULT"),
						'TIMING'=>GetMessage("codematrix.popup_COUPON_CONTENT_MAIN_TIMING_DEFAULT"),
						'EMAIL_SHOW'=>'Y',
						'EMAIL_PLACEHOLDER'=>GetMessage("codematrix.popup_CONTACT_EMAIL_PLACEHOLDER"),
						'EMAIL_ADD2BASE'=>'N',
						'EMAIL_NOT_NEW'=>'N',
						'EMAIL_EMAIL_TO'=>'N',
						'EMAIL_TEMPLATE'=>$serviceMessage[0]['ID'],
						'EMAIL_NOT_NEW_TEXT'=>GetMessage('codematrix.popup_COUPON_CONTENT_EMAIL_NOT_NEW_DEFAULT'),
						
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
						
					)
				),
			);
		}
		$serviceMessageRoulette=\CodeMatrix\Popup\Mail::getTemplates('CODEMATRIX_POPUP_ROULETTE_SEND');
		$templates['roulette']=array(
				array(
					'template'=>'default',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_ROULETTE_T1"),
					'active'=>true,
					'color_style'=>'blue',
					'color_styles'=>array(
						'blue'=>GetMessage("codematrix.popup_ACTION_COLOR_BLUE"),
						'green'=>GetMessage("codematrix.popup_ACTION_COLOR_GREEN"),
						'greensea'=>GetMessage("codematrix.popup_ACTION_COLOR_GREENSEA"),
						'orange'=>GetMessage("codematrix.popup_ACTION_COLOR_ORANGE"),
						'pumpkin'=>GetMessage("codematrix.popup_ACTION_COLOR_PUMPKIN"),
						'red'=>GetMessage("codematrix.popup_ACTION_COLOR_RED"),
						'wisteria'=>GetMessage("codematrix.popup_ACTION_COLOR_WISTERIA")
					),
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_TYPE_ROULETTE_TITLE_DEFAULT"),
						'SUBTITLE'=>GetMessage("codematrix.popup_TYPE_ROULETTE_SUBTITLE_DEFAULT"),
						'BUTTON_TEXT'=>GetMessage("codematrix.popup_TYPE_ROULETTE_BUTTON_DEFAULT"),
						'RESULT_TEXT'=>GetMessage("codematrix.popup_TYPE_ROULETTE_RESULT_DEFAULT"),
						'NOTHING_TEXT'=>GetMessage("codematrix.popup_TYPE_ROULETTE_NOTHING_DEFAULT"),
						'BUTTON_METRIC'=>'',
					)
				)
			 );
		if(\Bitrix\Main\Loader::IncludeModule('sale')){
			$templates['roulette'][0]['props']['TIMING']=GetMessage("codematrix.popup_COUPON_CONTENT_MAIN_TIMING_DEFAULT");
		}
		$templates['roulette'][0]['props']['MAIL_TEMPLATE']=$serviceMessageRoulette[0]['ID'];
		$templates['roulette'][0]['props']['EMAIL_SHOW']='Y';
		$templates['roulette'][0]['props']['EMAIL_PLACEHOLDER']=GetMessage("codematrix.popup_TYPE_ROULETTE_PLACEHOLDER_DEFAULT");
		$templates['roulette'][0]['props']['EMAIL_ADD2BASE']='N';
		$templates['roulette'][0]['props']['EMAIL_NOT_NEW']='N';
		$templates['roulette'][0]['props']['EMAIL_NOT_NEW_TEXT']=GetMessage('codematrix.popup_COUPON_CONTENT_EMAIL_NOT_NEW_DEFAULT');
		$templates['roulette'][0]['props']['POSITION_LEFT']='';
		$templates['roulette'][0]['props']['POSITION_RIGHT']='';
		$templates['roulette'][0]['props']['POSITION_TOP']='';
		$templates['roulette'][0]['props']['POSITION_BOTTOM']='';
		
		if (\Bitrix\Main\Loader::IncludeModule('sale')){
			$serviceMessageDiscount=\CodeMatrix\Popup\Mail::getTemplates('CODEMATRIX_POPUP_DISCOUNT_SEND');
			$templates['discount']=array(array(
				'template'=>'default',
					'name'=>GetMessage("codematrix.popup_TYPE_NAME_DISCOUNT_T1"),
					'active'=>true,
					
					'color_style'=>'ca_BleuDeFrance',
					'color_styles'=>array(
						''=>'...',
						'ca_LianHongLotusPink'=>GetMessage("codematrix.popup_ca_LianHongLotusPink"),
						'ca_DoubleDragonSkin'=>GetMessage("codematrix.popup_ca_DoubleDragonSkin"),
						'ca_Amour'=>GetMessage("codematrix.popup_ca_Amour"),
						'ca_Cyanite'=>GetMessage("codematrix.popup_ca_Cyanite"),
						'ca_DarkMountainMeadow'=>GetMessage("codematrix.popup_ca_DarkMountainMeadow"),
						'ca_AquaVelvet'=>GetMessage("codematrix.popup_ca_AquaVelvet"),
						'ca_BleuDeFrance'=>GetMessage("codematrix.popup_ca_BleuDeFrance"),
						'ca_Bluebell'=>GetMessage("codematrix.popup_ca_Bluebell"),
						'ca_StormPetrel'=>GetMessage("codematrix.popup_ca_StormPetrel"),
						'ca_ImperialPrimer'=>GetMessage("codematrix.popup_ca_ImperialPrimer")
					),
					'props'=>array(
						'TITLE'=>GetMessage("codematrix.popup_TYPE_DISCOUNT_TITLE_DEFAULT"),
						'SUBTITLE'=>GetMessage("codematrix.popup_TYPE_DISCOUNT_SUBTITLE_DEFAULT"),
						'IMG_1_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/discount_logo.png',
						'IMG_2_SRC'=>'/bitrix/themes/.default/codematrix.popup/preload/discount_girl.png',
						'BUTTON_TEXT'=>GetMessage("codematrix.popup_TYPE_DISCOUNT_BUTTON_DEFAULT"),
						'BUTTON_METRIC'=>'',
						'RULE_ID'=>'',
						'DISCOUNT_MASK'=>'0000#####',
						'USER_GROUP'=>'',
						
						
						'NAME_SHOW'=>'Y',
						'NAME_REQUIRED'=>'Y',
						'NAME_TITLE'=>GetMessage("codematrix.popup_DISCOUNT_NAME_TITLE"),
						
						'LASTNAME_SHOW'=>'Y',
						'LASTNAME_REQUIRED'=>'Y',
						'LASTNAME_TITLE'=>GetMessage("codematrix.popup_DISCOUNT_LASTNAME_TITLE"),

						'PHONE_SHOW'=>'Y',
						'PHONE_REQUIRED'=>'Y',
						'PHONE_TITLE'=>GetMessage("codematrix.popup_DISCOUNT_PHONE_TITLE"),
						
						'EMAIL_SHOW'=>'Y',
						'EMAIL_REQUIRED'=>'Y',
						'EMAIL_TITLE'=>GetMessage("codematrix.popup_DISCOUNT_EMAIL_TITLE"),
						'EMAIL_ADD2BASE'=>'Y',
						'EMAIL_EMAIL_TO'=>'Y',
						'EMAIL_TEMPLATE_D'=>$serviceMessageDiscount[0]['ID'],
						'EMAIL_NOT_NEW'=>'Y',
						'EMAIL_NOT_NEW_TEXT'=>GetMessage("codematrix.popup_DISCOUNT_EMAIL_NOT_NEW"),
						
						'USE_CONSENT_SHOW'=>'Y',
						'CONSENT_LIST'=>'1',
						
						'POSITION_LEFT'=>'',
						'POSITION_RIGHT'=>'',
						'POSITION_TOP'=>'',
						'POSITION_BOTTOM'=>'',
						
					)
				)
			);
		}
		
		$customTemplates=$this->getCustomTemplates();
		foreach($templates as $nextKey=>&$nextTemplate){
			if(!empty($customTemplates[$nextKey])){
				$nextTemplate=array_merge($nextTemplate, $customTemplates[$nextKey]);
			}
		}
		return $templates;
	}

	private function getCustomPreset(){
		$templates=$this->getTemplatesPreset();
		$templates=$this->getCustomColors($templates);
		//$templates=$this->getCustomTemplates($templates);
		return $templates;
	}

	private function getCustomTemplates(){
		$retArr=array();
		$res = \CodeMatrix\Popup\Model\PopupTemplateTable::getList(array(
			'select'=>array('ID', 'NAME', 'TYPE', 'TEMPLATE'),
			'order'=>array('ID'=>'ASC')
		));
		while($ormRow = $res->fetch()){
			$row = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);
			$additionalColorThemes[$row['template']]['custom_'.$row['id']]=$row['name'].' ['.$row['id'].']';
			$retArr[$row['type']][]=unserialize($row['template']);
		}
		return $retArr;
	}
	private function getCustomColors($templates){
		$additionalColorThemes=array();
		$res = \CodeMatrix\Popup\Model\PopupColorTable::getList(array(
			'select'=>array('ID', 'TEMPLATE', 'NAME'),
			'order'=>array('TEMPLATE'=>'ASC', 'ID'=>'ASC')
		));
		while($ormRow = $res->fetch()){
			$row = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);
			$additionalColorThemes[$row['template']]['custom_'.$row['id']]=$row['name'].' ['.$row['id'].']';
		}
		$types=$this->getTypesPreset();
		foreach($templates as $keyType=>&$nextType){
			foreach($nextType as $keyTemplate=>&$nextTemplate){
				if(empty($nextTemplate['color_styles']) && !empty($types[$keyType]['color_style'])){
					$nextTemplate['color_styles']=$types[$keyType]['color_style'];
				}
				if(!empty($nextTemplate['color_styles']) && !empty($additionalColorThemes[$keyType.'_'.$nextTemplate['template']])){
					$nextTemplate['color_styles']=array_merge($nextTemplate['color_styles'], $additionalColorThemes[$keyType.'_'.$nextTemplate['template']]);
				}
			}
		}
		return $templates;
	}


	public function getTemplates(){
		//$templates=$this->getTemplatesPreset();
		$templates=$this->getCustomPreset();
		if($this->idPopup!='new'){
			$settings=$this->getSetting($this->idPopup);

			foreach($templates[$settings['view']['type']] as &$nextTemplate){
				if($nextTemplate['template']==$settings['view']['template']){
					$nextTemplate['active']=true;
					$nextTemplate['color_style']=$settings['view']['color_style'];
					//$nextTemplate['props']=array();
					foreach($settings['view']['props'] as $keyProp=>$valProp){
						if(strpos($keyProp, 'IMG_')!==false && strpos($keyProp, '_id')!==false){
							continue;
						}
						if(strpos($keyProp, 'IMG_')!==false && strpos($keyProp, '_id')===false && intval($valProp)>0){
							$nextTemplate['props'][$keyProp.'_id']=$valProp;
							$valProp=CFile::GetPath($valProp);
						}
						if(strpos($keyProp, 'IMG_')!==false && strpos($keyProp, '_id')===false && empty($valProp)){
							$valProp=$nextTemplate['props'][$keyProp];
						}

						$nextTemplate['props'][$keyProp]=$valProp;
						/*if($keyProp=='HREF_TARGET'){
							$nextTemplate['props'][$keyProp]='123';
						}*/
					}
				}else{
					$nextTemplate['active']=false;
				}
			}
		}

		return $templates;
	}

	private function getConditionsPreset(){
		$conditionArr=array(
			'active'=>false,
			'sort'=>500,
			//'dateStart'=>ConvertTimeStamp(time(), "FULL", SITE_ID),
			//'dateFinish'=>ConvertTimeStamp(strtotime("+30 day"), "FULL", SITE_ID),
			'dateStart'=>'',
			'dateFinish'=>'',
			'sites'=>array(
				array('active'=>true, 'id'=>'all', 'name'=>GetMessage("codematrix.popup_CONDITIONS_SITEALL"))
			),
			'groups'=>array(
				array('active'=>false, 'id'=>'unregister', 'name'=>GetMessage("codematrix.popup_CONDITIONS_GROUPSUNREGISTER"))
			),
			'showOnlyPath'=>'',
			'hideOnlyPath'=>'',
			//'maskPriority'=>'SHOW',
			'afterShowCountPages'=>0,
			'afterTimeSecond'=>0,
			'timeInterval'=>'',//12:40#15:50
			'anchorVisible'=>'',//<a name="#anchorVisible#"></a>
			'onClickClassLink'=>'',
			'alreadygoing'=>false,
			'repeatTime'=>0, // Интервал повторного показа.
			'repeatTime_type'=>'day'//type of repeat
		);

		if(\Bitrix\Main\Loader::IncludeModule("statistic")){
			$conditionArr['groups'][]=array('active'=>false, 'id'=>'firstvisit', 'name'=>GetMessage("codematrix.popup_CONDITIONS_GROUPSFIRSTVISIT"));
		}

		$rsSites = CSite::GetList($by="sort", $order="desc");
		while ($arSite = $rsSites->Fetch()){
			$conditionArr['sites'][]=array('active'=>false, 'id'=>$arSite['LID'], 'name'=>$arSite['NAME']);
		}
		$rsGroups = \Bitrix\Main\GroupTable::getList(array(
			'select' => array('ID', 'NAME'),
			'order' => array('C_SORT' => 'ASC', 'ID' => 'ASC')
		));
		while($arGroup = $rsGroups->fetch()){
			$conditionArr['groups'][]=array('active'=>false, 'id'=>$arGroup['ID'], 'name'=>$arGroup['NAME']);
		}
		if(\Bitrix\Main\Loader::includeModule('sale')){
			$conditionArr['saleCountProduct']=0;
			$conditionArr['saleSummBasket']=0;
			$conditionArr['saleIDProdInBasket']=0;
		}
		return $conditionArr;
	}

	public function getConditions(){
		$conditionArr=$this->getConditionsPreset();
		if($this->idPopup!='new'){
			$settings=$this->getSetting($this->idPopup);
			foreach($conditionArr as $keyCond=>&$valCond){
				if($keyCond=='active' || $keyCond=='alreadygoing'){
					$valCond=($settings['condition'][$keyCond]=='Y')?true:false;
				}elseif($keyCond=='sites' || $keyCond=='groups'){
					foreach($valCond as $key=>$val){
						if(!empty($settings['condition'][$keyCond]) && in_array($val['id'], $settings['condition'][$keyCond])){
							$valCond[$key]['active']=true;
						}else{
							$valCond[$key]['active']=false;
						}
					}
				}else{
					$valCond=$settings['condition'][$keyCond];
				}
			}
			if(!empty($settings['contact'])){
				$conditionArr['contact']=$settings['contact'];
			}
			if(!empty($settings['timer'])){
				$conditionArr['timer']=$settings['timer'];
			}
			if(!empty($settings['roulett'])){
				$conditionArr['roulett']=$settings['roulett'];
			}
			$conditionArr['service_name']=$settings['service_name'];
		}
		return $conditionArr;
	}
	
	public function searchinMailList($mail,$id=0){
		$id=(int)$id;
		if(!empty($mail)){
			$connection = \Bitrix\Main\Application::getConnection();
			$conHelper = $connection->getSqlHelper();
			$tmpMail = $conHelper->forSql($mail);
			$filter = array(
				'filter'=>array('EMAIL'=>$tmpMail)
			);
			if($id>0){
				$groupList=\Bitrix\Sender\ListTable::GetList(array(
					'filter'=>array('CODE'=>'codematrixPopup_'.$id)
				));
				$emailList=\Bitrix\Sender\ContactTable::GetList($filter);
				$filter=array();
				if($row=$emailList->fetch()){
					$filter['filter']['CONTACT_ID']=$row['ID'];
				}else{
					return true;
				}
				if(!$row=$groupList->fetch()){
					$rowPopup=$this->getSetting($id);
					$listAddDb = \Bitrix\Sender\ListTable::add(array(
						'NAME' => $rowPopup['service_name'],
						'CODE' => 'codematrixPopup_'.$id,
					));
					if($listAddDb->isSuccess())
						$listId = $listAddDb->getId();
				}else{
					$listId = $row['ID'];
				}
					
				$filter['filter']['LIST_ID']=$listId;
			}
			$emailList=\Bitrix\Sender\ContactListTable::GetList($filter);
			if(!$row=$emailList->fetch()){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function insertToMailList($mail, $name, $idPopup=0){
		$tmpPopup=0;
		if((int) $idPopup>0){
			$tmpPopup=(int) $idPopup;
		}elseif((int) $this->idPopup>0){
			$tmpPopup=(int) $this->idPopup>0;
		}
		if($tmpPopup>0){
			$connection = \Bitrix\Main\Application::getConnection();
			$conHelper = $connection->getSqlHelper();
			$curDateFunc = new \Bitrix\Main\Type\DateTime;

			$rowPopup=$this->getSetting($tmpPopup);

			// Идентификатор группы пользователей.
			$groupList=\Bitrix\Sender\ListTable::GetList(array(
				'filter'=>array('CODE'=>'codematrixPopup_'.$tmpPopup)
			));
			if(!$row=$groupList->fetch()){
				$listAddDb = \Bitrix\Sender\ListTable::add(array(
					'NAME' => $rowPopup['service_name'],
					'CODE' => 'codematrixPopup_'.$tmpPopup,
				));
				if($listAddDb->isSuccess()){
					$listId = $listAddDb->getId();
				}
			}else{
				$listId = $row['ID'];
			}
			// Идентификатор почтового шаблона.
			$tmpMail = $conHelper->forSql($mail);
			$emailList=\Bitrix\Sender\ContactTable::GetList(array(
				'filter'=>array('EMAIL'=>$tmpMail)
			));
			if(!$row=$emailList->fetch()){
				$listAddDb = \Bitrix\Sender\ContactTable::add(array(
					'NAME' => $conHelper->forSql($name),
					'EMAIL' => $tmpMail,
					'DATE_INSERT' => $curDateFunc,
					'DATE_UPDATE' => $curDateFunc
				));
				if($listAddDb->isSuccess()){
					$mailId = $listAddDb->getId();
				}
			}else{
				$mailId = $row['ID'];
			}

			// Добавить группу пользователей в список рассылки.
			$unionList=\Bitrix\Sender\ContactListTable::GetList(array(
				'filter'=>array('LIST_ID'=>$listId, 'CONTACT_ID'=>$mailId)
			));
			if(!$row=$unionList->fetch()){
				$listAddDb = \Bitrix\Sender\ContactListTable::add(array(
					'LIST_ID' => $listId,
					'CONTACT_ID' => $mailId
				));
				if($listAddDb->isSuccess()){
					return true;
				}
			}else{
				return true;
			}
		}
		return false;
	}

	public function getSetting($id=0){
		if($id==0){return false;}
		$res = \CodeMatrix\Popup\Model\PopupTable::getList(array(
			'filter'=>array('=ID'=>$id),
			'select'=>array('*'),
			'limit'=>1
		));
		$ormRow = $res->fetch();
		$row = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);
		if($row){
			 $retArr=unserialize($row['settings']);
			 $retArr['service_name']=$row['name'];
			 $retArr['row']=$row;
			 //var_dump($retArr['view']['type']);
			 if($retArr['view']['type']=='coupon'){
				CModule::IncludeModule("sale");
				$res=CSaleDiscount::GetByID($retArr['view']['props']['RULE_ID']);
				$retArr['view']['props']['PERCENT']=$res;
				//if($res['DISCOUNT_TYPE']=='P'){
					$retArr['view']['props']['PERCENT']=explode('=>',$res['APPLICATION']);//['DISCOUNT_VALUE'].'%';
					$type=explode(',',$retArr['view']['props']['PERCENT'][2]);
					$type=$type[0];
					$retArr['view']['props']['PERCENT']=explode(',',$retArr['view']['props']['PERCENT'][1]);
					$retArr['view']['props']['PERCENT']=(float)$retArr['view']['props']['PERCENT'][0]*(-1);
					if($type[2]=='P'){
						$retArr['view']['props']['PERCENT']=$retArr['view']['props']['PERCENT'].'%';
					}elseif($type[2]=='S'||$type[2]=='F'){
						$retArr['view']['props']['PERCENT']=CurrencyFormat($retArr['view']['props']['PERCENT'],$res['CURRENCY']);
					}
				//}
			 }
			 return $retArr;
		 }
		 return false;
	}
	
	public function getCoupon($id,$avaliable,$email='',$popup_id=0,$result_text='',$mask=''){
		if(empty($id)||$id==0){return false;}
		if($id!='win'){
			$COUPON='';
			$cTime=time();
			$startTime=new Bitrix\Main\Type\DateTime(ConvertTimeStamp($cTime, "FULL"));
			$endTime=false;
			if($avaliable!=''&&$avaliable!='infinite'){
				$endTime = $endTime=AddToTimeStamp(array('DD'=>$avaliable), $cTime);
				$endTime=new Bitrix\Main\Type\DateTime(ConvertTimeStamp($endTime, "FULL"));
			}
			$fields = array(
				'DISCOUNT_ID'=>$id,
				'ACTIVE'=>'Y',
				'COUPON'=>$COUPON,
				'DATE_APPLY'=>false,
				'ACTIVE_TO'=>$endTime,
				'ACTIVE_FROM'=>$startTime,
				'DESCRIPTION'=>$email,
			);
			if($avaliable==='infinite'){
				global $USER;
				$fields['TYPE']=Bitrix\Sale\Internals\DiscountCouponTable::TYPE_MULTI_ORDER;
				$fields['MAX_USE']='';
				$fields['USER_ID']=$USER->GetID();
				$res=\Bitrix\Sale\Internals\DiscountCouponTable::GetList(array('filter'=>array('DISCOUNT_ID'=>$id),'order'=>array('ID'=>'desc')));
				$prevCoupon=str_replace('%23','0',$mask);
				$prevCoupon=str_replace('#','0',$prevCoupon);
				$mask_length=strlen($prevCoupon);
				if($r=$res->fetch()){
					$prevCoupon=$r['COUPON'];
					if($mask_length>strlen($prevCoupon)){
						$len=strlen($prevCoupon);
						$prevCoupon=substr($mask,($mask_length-$len)).$prevCoupon;
					}
				}
				$mask=str_replace('%23','',$mask);
				$mask=str_replace('#','',$mask);
				$mask_length_=strlen($mask);
				$prevCoupon=substr($prevCoupon,$mask_length_);
				$newCoupon=(int)$prevCoupon;
				$newCoupon++;
				$couponLen=strlen($newCoupon);
				$prevCouponLen=strlen($prevCoupon);
				while($prevCouponLen>$couponLen){
					$newCoupon='0'.$newCoupon;
					$couponLen++;
				}
				$COUPON = $mask.$newCoupon;
			}else{
				$COUPON = Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
				$fields['TYPE']=Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER;
				$fields['MAX_USE']='1';
			}
			$fields['COUPON']=$COUPON;
			if($popup_id!=0){
				$settings=$this->getSetting($popup_id);
				if($settings['view']['type']=='coupon'){
					if(!empty($settings['view']['props']['EMAIL_TEMPLATE'])){
						if($email!=''){
						Event::send(array(
								"EVENT_NAME" => "CODEMATRIX_POPUP_SEND_COUPON",
								"LID" => $this->site_id,
								"C_FIELDS" => array(
									"EMAIL" => $email,
									"COUPON" => $COUPON,
								),
								'MESSAGE_ID'=>$settings['view']['props']['EMAIL_TEMPLATE']
							));
						}
					}
				}elseif($settings['view']['type']=='roulette'){
					if(!empty($settings['view']['props']['MAIL_TEMPLATE'])){
						if($email!=''){
							Event::send(array(
								"EVENT_NAME" => "CODEMATRIX_POPUP_ROULETTE_SEND",
								"LID" => $this->site_id,
								"C_FIELDS" => array(
									"EMAIL" => $email,
									"COUPON" => $COUPON,
									"RESULT_TEXT"=>$result_text,
								),
								'MESSAGE_ID'=>$settings['view']['props']['MAIL_TEMPLATE']
							));
						}
					}
				}elseif($settings['view']['type']=='discount'){
					if(!empty($settings['view']['props']['EMAIL_TEMPLATE_D'])){
						if($email!=''){
							Event::send(array(
								"EVENT_NAME" => "CODEMATRIX_POPUP_DISCOUNT_SEND",
								"LID" => $this->site_id,
								"C_FIELDS" => array(
									"EMAIL" => $email,
									"COUPON" => $COUPON,
									"NAME"=>$USER->GetFirstName(),
									"LAST_NAME"=>$USER->GetLastName(),
								),
								'MESSAGE_ID'=>$settings['view']['props']['EMAIL_TEMPLATE_D']
							));
						}
					}
				}
			}
			$couponsResult = \Bitrix\Sale\Internals\DiscountCouponTable::add($fields);
			return $COUPON;
		}elseif($id=='win'){
			$COUPON=='';
			if($popup_id!=0){
				$settings=$this->getSetting($popup_id);
				if(!empty($settings['view']['props']['MAIL_TEMPLATE'])){
					if($email!=''){
						Event::send(array(
							"EVENT_NAME" => "CODEMATRIX_POPUP_ROULETTE_SEND",
							"LID" => $this->site_id,
							"C_FIELDS" => array(
								"EMAIL" => $email,
								"COUPON" => $COUPON,
								"RESULT_TEXT"=>$result_text,
							),
							'MESSAGE_ID'=>$settings['view']['props']['MAIL_TEMPLATE']
						));
					}
				}
			}
		}
	}
	// Публичная часть модуля.

	public static function convertTimeFromSecond($tm){
		$tmStr='';
		if($tm>86400){
			$tmStr.=floor($tm/86400).' '.GetMessage("codematrix.popup_TIME_DAYS").' ';
			$tm=$tm%86400;
		}
		if($tm>3600){
			$tmStr.=floor($tm/3600).' '.GetMessage("codematrix.popup_TIME_HOURS").' ';
			$tm=$tm%3600;
		}
		if($tm>60){
			$tmStr.=floor($tm/60).' '.GetMessage("codematrix.popup_TIME_MINUTES").' ';
			$tm=$tm%60;
		}
		if($tm>0){
			$tmStr.=$tm.' '.GetMessage("codematrix.popup_TIME_SECONDS");
		}
		return $tmStr;
	}

	/**
	* Добавляет данные всплывающих окон для показа в публичной части сайта.
	*/
	public static function insertPopups(){
		if(!defined('ADMIN_SECTION') && empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
			$afterTimeSecond=0;
			if(!empty($_SESSION['flp_popup_afterTimeSecond'])){
				$afterTimeSecond=time()-$_SESSION['flp_popup_afterTimeSecond'];
			}else{
				$_SESSION['skwb24_popup_afterTimeSecond']=time();
				$afterTimeSecond=0;
			}
			$alreadyShow=array();
			if(!empty($_SESSION['alreadyShow'])){
				$alreadyShow=$_SESSION['alreadyShow'];
			}
			$codematrixPopups=array("site"=>SITE_ID, "afterTimeSecond"=>$afterTimeSecond, "alreadyShow"=>$alreadyShow);
			if(\Bitrix\Main\Loader::includeModule('sale')){
				$codematrixPopups['basket']=self::GetBasketInfo();
			}
			CJSCore::Init(array("ajax", "popup"));
			
			global $APPLICATION;
			$APPLICATION->AddHeadScript('/bitrix/js/'.self::idModule.'/script_public.js');
			$APPLICATION->SetAdditionalCSS('/bitrix/js/main/core/css/core_popup.css');
			$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/codematrix.popup_public.css');
			$APPLICATION->AddHeadString('<script> var codematrixPopups='.json_encode($codematrixPopups).'; </script>');
			
			/*Asset::getInstance()->addJs('/bitrix/js/'.self::idModule.'/script_public.js');
			Asset::getInstance()->addCss('/bitrix/js/main/core/css/core_popup.css');
			Asset::getInstance()->addCss('/bitrix/themes/.default/codematrix.popup_public.css');
			Asset::getInstance()->addString('<script> var codematrixPopups='.json_encode($codematrixPopups).'; </script>');*/
		}
	}

	public static function GetBasketInfo(){
		$basket=array('products'=>array(), 'summ'=>0);
		$basketNum=CSaleBasket::GetBasketUserID(true);
		$tmpOffers=array();
		if(!empty($basketNum)){
			$dbBasketItems = CSaleBasket::GetList(array(),	array("FUSER_ID" =>$basketNum, "LID" => SITE_ID, "ORDER_ID" => "NULL"), false, false, array("*"));
			while($arItems = $dbBasketItems->Fetch()){
				$basket['products'][]=$arItems['PRODUCT_ID'];
				$basket['summ']+=$arItems['PRICE']*$arItems['QUANTITY'];
				if($arItems['PRODUCT_XML_ID']!=$arItems['PRODUCT_ID']){
					$tmpOffers[]=$arItems['PRODUCT_ID'];
				}
			}
			if(count($tmpOffers)>0){
				$prods=CCatalogSKU::getProductList($tmpOffers);
				foreach($prods as $nextProduct){
					$basket['products'][]=$nextProduct['ID'];
				}
			}
		}
		return $basket;
	}

	public function getAvailablePopups($options){

		$res = \CodeMatrix\Popup\Model\PopupTable::getList(array(
			'filter'=>array('=ACTIVE'=>'Y'),
			'select'=>array('*'),
			'order'=>array('SORT'=>'ASC')
		));
		$retArr=array();

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		
		$httpType=($request->isHttps())?'https://':'http://';
		$tmp = $_SERVER['HTTP_HOST'];
		$tmp = explode(':',$tmp);
		$tmp = $tmp[0];
		$serverBase=$httpType.$tmp;
		$options['pageUrl'] = str_replace($serverBase, "", $options['pageUrl']);
	
		while($ormRow = $res->fetch()){
			$row = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);

			$settings=unserialize($row['settings']);

			// Не показывать окно пользователям, уже заполнившим форму.
			$cCookie=$request->getCookie("codematrixPopupFilling_".$row['id']);
			if(!empty($cCookie) &&($settings["view"]["type"]=='contact'||$settings["view"]["type"]=='age'||$settings["view"]["type"]=='roulette'||$settings["view"]["type"]=='discount')){
				continue;
			}

			// Окно не предназначено для текущего сайта.
			if($settings['condition']['sites'][0]!='all' & !in_array($options['site'], $settings['condition']['sites'])){
				continue;
			}
			// Текущая дата не соответствует дате начала показа.
			if(!empty($settings['condition']['dateStart']) && $settings['condition']['dateStart']>$options['dateUser']){
				continue;
			}
			// Текущая дата не соответствует дате окончания показа.
			if(!empty($settings['condition']['dateFinish']) && $settings['condition']['dateFinish']<=$options['dateUser']){
				continue;
			}
			if(!empty($settings['timer']['enabled'])&&$settings['timer']['enabled']=="Y"){
				$format = 'd.m.Y H:i:s';
				$unixtime=DateTime::createFromFormat($format, $settings['timer']['date']);
				if(time()>$unixtime->getTimestamp()){
					continue;
				}
			}
			// Группа пользователя не соответствует условиям показа.
			if(!empty($settings['condition']['groups'])){
				$userAccess=false;
				global $USER;
				if(!$USER->IsAuthorized() && in_array('unregister', $settings['condition']['groups'])){
					$userAccess=true;
				}elseif(in_array('firstvisit', $settings['condition']['groups']) && !empty($_SESSION["SESS_GUEST_NEW"]) && $_SESSION["SESS_GUEST_NEW"]=='Y'){
					$userAccess=true;
				}else{
					$cuserGroup=$USER->GetUserGroupArray();
					$tmpIntersect=array_intersect($cuserGroup, $settings['condition']['groups']);
					if(count($tmpIntersect)>0){
						$userAccess=true;
					}
				}
				if(!$userAccess){
					continue;
				}
			}
			// Адрес страницы не соответствует условиям показа.
			/*$showOnlyPath=true;
			$hideOnlyPath=false;
			$maskPriority=(empty($settings['condition']['maskPriority']))?'SHOW':$settings['condition']['maskPriority'];

			if(!empty($settings['condition']['showOnlyPath'])){
				$showOnlyPath=false;
				$tmpShowOnly=explode(PHP_EOL, $settings['condition']['showOnlyPath']);
				foreach($tmpShowOnly as $nextShowOnly){
					$nextShowOnly=trim($nextShowOnly);
					if(!empty($nextShowOnly)){
						if(strpos($nextShowOnly,  '*')!==false){
							$pattern = '|^'.str_replace(array('*', '?'), array('(.*)', '\?'), $nextShowOnly).'|';
							if(preg_match($pattern, $options['pageUrl'], $matches)==1){
								$showOnlyPath=true;
								break;
		 
							}
						}elseif($options['pageUrl']==$nextShowOnly){
							$showOnlyPath=true;
							break;
						}
					}
				}
				if($maskPriority=='SHOW' && $showOnlyPath==false){
					continue;
				}
			}

			if(!empty($settings['condition']['hideOnlyPath'])){
				$tmpHideOnly=explode(PHP_EOL, $settings['condition']['hideOnlyPath']);
				foreach($tmpHideOnly as $nextHideOnly){
					if(!empty($nextHideOnly)){
						$nextHideOnly=trim($nextHideOnly);
						if(strpos($nextHideOnly,  '*')!==false){
							$pattern = '|^'.str_replace(array('*', '?'), array('(.*)', '\?'), $nextHideOnly).'|';
							if(preg_match($pattern, $options['pageUrl'], $matches)==1){
								$hideOnlyPath=true;
								break;
							}
						}elseif($options['pageUrl']==$nextHideOnly){
							$hideOnlyPath=true;
							break;
						}
					}
				}
				if($maskPriority=='NOTSHOW' && $hideOnlyPath==true){
					continue;
				}
			}

			if($maskPriority=='SHOW' && $showOnlyPath==true){

			}elseif($hideOnlyPath==true || $showOnlyPath==false){
				continue;
			}*/
			
			if(!empty($settings['condition']['showOnlyPath'])){
				$showOnlyPath=false;
				$tmpShowOnly=explode(PHP_EOL, $settings['condition']['showOnlyPath']);
				foreach($tmpShowOnly as $nextShowOnly){
					$nextShowOnly=trim($nextShowOnly);
					if(!empty($nextShowOnly)){
						if(strpos($nextShowOnly,  '*')!==false){
							$pattern = '|^'.str_replace(array('*', '?'), array('(.*)', '\?'), $nextShowOnly).'|';
							if(preg_match($pattern, $options['pageUrl'], $matches)==1){
								$showOnlyPath=true;
								break;
							}
						}elseif($options['pageUrl']==$nextShowOnly){
							$showOnlyPath=true;
							break;
						}
					}
				}
				if($showOnlyPath==false){
					continue;
				}
			}

			if(!empty($settings['condition']['hideOnlyPath'])){
				$hideOnlyPath=false;
				$tmpHideOnly=explode(PHP_EOL, $settings['condition']['hideOnlyPath']);
				foreach($tmpHideOnly as $nextHideOnly){
					if(!empty($nextHideOnly)){
						$nextHideOnly=trim($nextHideOnly);
						if(strpos($nextHideOnly,  '*')!==false){
							$pattern = '|^'.str_replace(array('*', '?'), array('(.*)', '\?'), $nextHideOnly).'|';
							if(preg_match($pattern, $options['pageUrl'], $matches)==1){
								$hideOnlyPath=true;
								break;
							}
						}elseif($options['pageUrl']==$nextHideOnly){
							$hideOnlyPath=true;
							break;
						}
					}
				}
				if($hideOnlyPath==true){
					continue;
				}
			}

			// Не выполнено условие показа после заданного количества просмотренных страниц.
			if(!empty($settings['condition']['afterShowCountPages']) && $options['countPages']<=$settings['condition']['afterShowCountPages']){
				continue;
			}
			$tmpPropsCondition=array('repeatTime','repeatTime_type', 'afterTimeSecond', 'timeInterval', 'alreadygoing', 'anchorVisible', 'onClickClassLink', 'saleCountProduct', 'saleSummBasket', 'saleIDProdInBasket');
			foreach($tmpPropsCondition as $nextCond){
				//if(!empty($settings['condition'][$nextCond])){
					if($nextCond=='afterTimeSecond' && !empty($_SESSION['skwb24_popup_afterTimeSecond'])){
						$settings['condition'][$nextCond]=$settings['condition'][$nextCond]-(time()-$_SESSION['skwb24_popup_afterTimeSecond']);
					}
					if($nextCond!='afterTimeSecond' && empty($settings['condition'][$nextCond])){
						continue;
					}
					$retArr[$row['id']][$nextCond]=$settings['condition'][$nextCond];
				//}
			}
		}
		return $retArr;
	}

	/**
	* Формирует массив данных для компонентов.
	*/
	public function getComponentResult($idPopup){
		if($idPopup==0){return false;}
		$res = \CodeMatrix\Popup\Model\PopupTable::getList(array(
			'filter'=>array('=ID'=>$idPopup),
			'select'=>array('*'),
			'limit'=>1
		));
		$ormRow = $res->fetch();
		$row = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);
		if($row){
			$settings=unserialize($row['settings']);

			// В старых или неполных записях может отсутствовать выбранный шаблон.
			if(empty($settings['view']['template']) && !empty($settings['view']['type'])){
				$templates=$this->getTemplatesPreset();
				if(!empty($templates[$settings['view']['type']][0]['template'])){
					$settings['view']['template']=$templates[$settings['view']['type']][0]['template'];
				}
			}

			$settings['view']['props']['THEME']= $settings['view']['color_style'];
			$settings['view']['props']['TEMPLATE_NAME']= $settings['view']['type'].'_'.$settings['view']['template'];

			foreach($settings['view']['props'] as $keyProp=>$nextProp){
				if(strpos($keyProp, 'IMG_')!==false && intval($nextProp)>0){
					$settings['view']['props'][$keyProp]=CFile::GetPath($nextProp);
				}elseif(strpos($keyProp, 'IMG_')!==false && empty($nextProp)){
					$tmpTemplates=$this->getTemplates();
					foreach($tmpTemplates[$settings['view']['type']] as $nextTemplate){
						if($nextTemplate['template']==$settings['view']['template']){
							$settings['view']['props'][$keyProp]=$nextTemplate['props'][$keyProp];
							break;
						}
					}
				}
			}

			return  $settings['view']['props'];
		}
	}

	public function getHTMLByPopup($idPopup){
		$componentResult=$this->getComponentResult($idPopup);
		if(empty($componentResult['TEMPLATE_NAME'])){
			return false;
		}
		global $APPLICATION;
		$APPLICATION->IncludeComponent(
			"codematrix:popup.pro", $componentResult['TEMPLATE_NAME'],
			Array(
				"ID_POPUP" => $idPopup
			)
		);
	}

	public function getComponentPath($idPopups){
		if(count($idPopups)==0){return false;}
		foreach($idPopups as $nextPopup){
			$settings=$this->getComponentResult($nextPopup);
			$tmpComponent = new CBitrixComponent();
			$tmpComponent->InitComponent('codematrix:popup.pro', $settings['TEMPLATE_NAME']);
			$tmpComponent->initComponentTemplate();
			$tmpPath=$tmpComponent->__template->GetFolder();

			$retArr[$nextPopup]=array(
				'TEMPLATE'=>$tmpPath,
				'STYLE'=>$tmpPath.'/style.css',
				'TEMPLATE_NAME'=>$settings['TEMPLATE_NAME']
			);
			$settingsPos=$this->getSetting($nextPopup);
			$positions=array('POSITION_BOTTOM', 'POSITION_LEFT', 'POSITION_RIGHT', 'POSITION_TOP', 'VIDEO_AUTOPLAY');
			foreach($positions as $nextPosition){
				if(!empty($settingsPos['view']['props'][$nextPosition])){
					$retArr[$nextPopup][$nextPosition]=$settingsPos['view']['props'][$nextPosition];
				}
			}

			if(!empty($settings['THEME'])){
				$retArr[$nextPopup]['THEME']=$tmpPath.'/themes/'.$settings['THEME'].'.css';
			}
		}
		return $retArr;
	}

	public function setStatistic($idPopup, $value, $field){
		$statisticFields=array(
			'stat_show'=>'STAT_SHOW',
			'stat_time'=>'STAT_TIME',
			'stat_action'=>'STAT_ACTION'
		);
		if(!empty($field) && isset($statisticFields[$field])){
			$ormRow = \CodeMatrix\Popup\Model\PopupTable::getList(array(
				'filter'=>array('=ID'=>$idPopup),
				'select'=>array('ID', $statisticFields[$field]),
				'limit'=>1
			))->fetch();
			$row = \CodeMatrix\Popup\Model\LegacyRowMapper::toLegacyRow($ormRow);
			if($row){
				\CodeMatrix\Popup\Model\PopupTable::update($idPopup, array(
					$statisticFields[$field]=>$row[$field]+$value
				));
				return true;
			}
		}
		return false;
	}
}
?>
