<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["GROUPS"] = array_chunk($arResult["ITEMS"], 3);
$arResult['REVIEWS_COUNT'] = CIBlock::GetElementCount($arParams['IBLOCK_ID']);