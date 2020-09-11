<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(\Bitrix\Main\Loader::includeModule('sib.core')){
    foreach($arResult["ITEMS"] as &$arItem){
        $arItem['RATING_CLASS'] =  \Sib\Core\BlogRating::getRatingClass((int)$arItem['PROPERTIES']['BLOG_LIKES']['VALUE']);
        $arItem["DISPLAY_ACTIVE_FROM"] = strtolower(str_replace(date('Y'), '', $arItem["DISPLAY_ACTIVE_FROM"]));
    }    
}
