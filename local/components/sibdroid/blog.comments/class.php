<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader; 
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;
use Sib\Core\BlogComments as BC;

\Bitrix\Main\Loader::includeModule('sib.core');

class CSibdroidBlog extends CBitrixComponent
{
    private $hasErrors = false;
    private $tdStamp = 0;
    private $ysStamp = 0;

    public function onPrepareComponentParams($arParams)
    {
        if((int)$arParams['ELEMENT_ID'] <= 0){
            $this->hasErrors = true;
        }      

        $arParams['AJAX_URL'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__) . '/ajax.php';

        $this->arResult['JS_PARAMS'] = CUtil::PHPToJSObject($arParams);

        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->startResultCache()) {
            //$this->arResult['PHP_RESULT'] = BC::getComments($this->arParams['ELEMENT_ID']);
            //$this->arResult['JS_RESULT'] = CUtil::PHPToJSObject($this->arResult['PHP_RESULT']);
            $this->includeComponentTemplate();
        } 
    }
}?>