<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
if (empty($arResult['ITEMS'])) return;

// AJAX PATH
$ajaxPath = SITE_DIR."ajax/sib/catalog.php";
$ajaxPathCompare = SITE_DIR."ajax/sib/compare_sib.php";
$ajaxPathFavorite = SITE_DIR."ajax/sib/favorites.php";
$arResult['ADD_URL_TEMPLATE'] = $ajaxPath."?".$arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";
$arResult['BUY_URL_TEMPLATE'] = $ajaxPath."?".$arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";
$arResult['COMPARE_URL_TEMPLATE'] = $ajaxPathCompare."?".$arParams["ACTION_VARIABLE"]."=ADD_TO_COMPARE_LIST&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";
$arResult['COMPARE_URL_TEMPLATE_DEL'] = $ajaxPathCompare."?".$arParams["ACTION_VARIABLE"]."=DELETE_FROM_COMPARE_LIST&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";

$arResult['FAVORITE_URL_TEMPLATE'] = $ajaxPathFavorite."?ACTION=ADD&ID=#ID#";
$arResult['FAVORITE_URL_TEMPLATE_DEL'] = $ajaxPathFavorite."?ACTION=DELETE&ID=#ID#";

$arResult['CURRENCY'] = CModule::IncludeModule("currency");
$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']) && $arResult['CURRENCY'];
if (!$boolConvert)
	$strBaseCurrency = CCurrency::GetBaseCurrency();

foreach($arResult['ITEMS'] as $index => $arItem)
{
	$arItem = CRZBitronic2CatalogUtils::processItemCommon($arItem);

	if (!empty($arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'])) {
		$imgAlt = $arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
	} else {
		$imgAlt = $arItem['NAME'];
	}
	$arItem['PICTURE_PRINT']['ALT'] = $imgAlt;
	$arItem['PICTURE_PRINT']['SRC'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['ID'], $arParams['RESIZER_SECTION']);

	$arItem['ON_REQUEST'] = (empty($arItem['MIN_PRICE']) || $arItem['MIN_PRICE']['VALUE'] <= 0);

    if (CRZBitronic2CatalogUtils::checkAvPrFotoForElement($arItem, $arParams)){
        unset($arResult['ITEMS'][$index]);
        continue;
    }

    // offers
	if (isset($arItem['OFFERS']) && !empty($arItem['OFFERS'])) {
		CRZBitronic2CatalogUtils::fillMinPriceFromOffers(
			$arItem,
			$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency,
			$bForOrder = false
		);
	}

	if ($arItem['ON_REQUEST']) {
		$arItem['CAN_BUY'] = false;
	}

    if (\Bitrix\Main\Loader::IncludeModule('yenisite.core')) {
        $catalogParams = \Yenisite\Core\Ajax::getParams('bitrix:catalog', false, CRZBitronic2CatalogUtils::getCatalogPathForUpdate());
        $arItem['CNT_REVIEWS'] = CRZBitronic2CatalogUtils::getCntReviewsOfItem($catalogParams,$arItem);
    }

	$arResult['ITEMS'][$index] = $arItem;
}
