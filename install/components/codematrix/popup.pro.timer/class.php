<?
use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application,
	Bitrix\Main\Web\Cookie,
	Bitrix\Main\Context;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

class CodeMatrixPopupTimer extends \\CBitrixComponent
{
    /**
     * Подготавливает параметры компонента таймера.
     *
     * @param array $params Параметры, переданные компоненту.
     *
     * @return array Подготовленные параметры компонента.
     */
    public function onPrepareComponentParams($params)
    {
        return $params;
    }

    /**
     * Формирует данные таймера и подключает шаблон компонента.
     *
     * @return void
     */
    public function executeComponent()
    {
        $this->arResult = array();
        $this->arResult['TITLE'] = $this->arParams['TITLE'];
        $this->arResult['TIME'] = $this->arParams['TIME'];

        $this->includeComponentTemplate();
    }
}
