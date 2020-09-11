<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(\Bitrix\Main\Loader::includeModule('sib.core')){
    $arResult['RATING_CLASS'] =  \Sib\Core\BlogRating::getRatingClass((int)$arResult['PROPERTIES']['BLOG_LIKES']['VALUE']);
    $arResult["DISPLAY_ACTIVE_FROM"] = strtolower(str_replace(date('Y'), '', $arResult["DISPLAY_ACTIVE_FROM"]));
    $arResult['DETAIL_TEXT'] = \Sib\Core\Helper::initLazyLoadImgFromText($arResult['DETAIL_TEXT'], 'blog_item_' . $arResult['ID'], ['WEBP' => 50, 'JPG' => 51]);

}
