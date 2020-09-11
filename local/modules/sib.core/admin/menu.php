<?
IncludeModuleLangFile(__FILE__); 

if($APPLICATION->GetGroupRight("main") >= "Q")
{
    $aMenu = [];
    if(\Bitrix\Main\Loader::includeModule('sib.core')){
        \Sib\Core\Ask::getAskMenuArr($aMenu);
    }
    return $aMenu;
}

return false;