<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
// AJAX PATH
$ajaxPath = SITE_DIR."ajax/catalog.php";
$ajaxPathCompare = SITE_DIR."ajax/compare.php";
$ajaxPathFavorite = SITE_DIR."ajax/favorites.php";
$arResult['ADD_URL_TEMPLATE'] = $ajaxPath."?".$arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";
$arResult['BUY_URL_TEMPLATE'] = $ajaxPath."?".$arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";
$arResult['COMPARE_URL_TEMPLATE'] = $ajaxPathCompare."?".$arParams["ACTION_VARIABLE"]."=ADD_TO_COMPARE_LIST&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";
$arResult['COMPARE_URL_TEMPLATE_DEL'] = $ajaxPathCompare."?".$arParams["ACTION_VARIABLE"]."=DELETE_FROM_COMPARE_LIST&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";

$arResult['FAVORITE_URL_TEMPLATE'] = $ajaxPathFavorite."?ACTION=ADD&ID=#ID#";
$arResult['FAVORITE_URL_TEMPLATE_DEL'] = $ajaxPathFavorite."?ACTION=DELETE&ID=#ID#";

$arResult['CURRENCY'] = CModule::IncludeModule("currency");
$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']) && $arResult['CURRENCY'];
if (!$boolConvert) {
	$strBaseCurrency = $arResult['CURRENCY'] ? CCurrency::GetBaseCurrency() : 'RUB';
}
$arResult['NO_ARTICUL'] = true;

if (CModule::IncludeModule('yenisite.market')) {
	$arResult['CHECK_QUANTITY'] = (CMarketCatalog::UsesQuantity($arParams['IBLOCK_ID']) == 1);
}

$arParams['USE_PRICE_COUNT'] = ($arParams['USE_PRICE_COUNT_'] === 'Y');

foreach($arResult['ITEMS'] as $index => $arItem)
{
	$arResult['ITEMS'][$index] = $arItem = CRZBitronic2CatalogUtils::processItemCommon($arItem);
	$arResult['ITEMS'][$index]['bFirst'] = $index == 0;
	if(!empty($arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT']))
	{
		$imgAlt = $arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
	}
	else
	{
		$imgAlt = $arItem['NAME'];
	}
	$arResult['ITEMS'][$index]['PICTURE_PRINT']['ALT'] = $imgAlt;
	$arResult['ITEMS'][$index]['PICTURE_PRINT']['SRC'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['ID'], $arParams['RESIZER_SECTION']);
		
	$arResult['ITEMS'][$index]['CHECK_QUANTITY'] = false;
	if (!isset($arItem['CATALOG_MEASURE_RATIO']))
		$arResult['ITEMS'][$index]['CATALOG_MEASURE_RATIO'] = 1;
	if (!isset($arItem['CATALOG_QUANTITY']))
		$arResult['ITEMS'][$index]['CATALOG_QUANTITY'] = 0;
	$arResult['ITEMS'][$index]['CATALOG_QUANTITY'] = (
		0 < $arResult['ITEMS'][$index]['CATALOG_QUANTITY'] && is_float($arResult['ITEMS'][$index]['CATALOG_MEASURE_RATIO'])
		? floatval($arResult['ITEMS'][$index]['CATALOG_QUANTITY'])
		: intval($arResult['ITEMS'][$index]['CATALOG_QUANTITY'])
	);
	$arResult['ITEMS'][$index]['CATALOG'] = false;
	if (!isset($arItem['CATALOG_SUBSCRIPTION']) || 'Y' != $arItem['CATALOG_SUBSCRIPTION'])
		$arResult['ITEMS'][$index]['CATALOG_SUBSCRIPTION'] = 'N';

	if ($arResult['MODULES']['catalog'])
	{
		$arResult['ITEMS'][$index]['CATALOG'] = true;
		if (!isset($arItem['CATALOG_TYPE']))
			$arResult['ITEMS'][$index]['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
		if (
			(CCatalogProduct::TYPE_PRODUCT == $arResult['ITEMS'][$index]['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arResult['ITEMS'][$index]['CATALOG_TYPE'])
			&& !empty($arItem['OFFERS'])
		)
		{
			$arResult['ITEMS'][$index]['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
		}
		switch ($arResult['ITEMS'][$index]['CATALOG_TYPE'])
		{
			case CCatalogProduct::TYPE_SKU:
				break;
			case CCatalogProduct::TYPE_SET:
				$arResult['ITEMS'][$index]['OFFERS'] = array();
				//no break;
			case CCatalogProduct::TYPE_PRODUCT:
			default:
				$arResult['ITEMS'][$index]['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
				$arResult['ITEMS'][$index]['FOR_ORDER']      = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'Y' == $arItem['CATALOG_CAN_BUY_ZERO'] && 0 >= $arResult['ITEMS'][$index]['CATALOG_QUANTITY']);
				break;
		}
	}
	else
	{
		$arResult['ITEMS'][$index]['CATALOG_TYPE'] = 0;
		$arResult['ITEMS'][$index]['OFFERS'] = array();

		//Prices for MARKET
		if (CModule::IncludeModule('yenisite.bitronic2lite') && CModule::IncludeModule('yenisite.market')) {
			$prices = CMarketPrice::GetItemPriceValues($arItem['ID'], $arItem['PRICES']);
			if (count($prices) > 0) {
				unset($arResult['ITEMS'][$index]['PRICES']);
			}
			$minPrice = false;
			foreach ($prices as $k => $pr) {
				$pr = floatval($pr);
				$arResult['ITEMS'][$index]['PRICES'][$k]['VALUE'] = $pr;
				$arResult['ITEMS'][$index]['PRICES'][$k]['PRINT_VALUE'] = $pr;
				if ((empty($minPrice) || $minPrice > $pr) && $pr > 0) {
					$minPrice = $pr;
				}
			}
			if ($minPrice !== false) {
				$arResult['ITEMS'][$index]['MIN_PRICE']['VALUE'] = $arItem['MIN_PRICE']['VALUE'] = $minPrice;
				$arResult['ITEMS'][$index]['MIN_PRICE']['PRINT_VALUE'] = $minPrice;
				$arResult['ITEMS'][$index]['MIN_PRICE']['DISCOUNT_VALUE'] = $minPrice;
				$arResult['ITEMS'][$index]['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] = $minPrice;
				$arResult['ITEMS'][$index]['CATALOG_MEASURE_RATIO'] = 1;
				$arResult['ITEMS'][$index]['CAN_BUY'] = $arItem['CAN_BUY'] = true;
			}
			$arResult['ITEMS'][$index]['CHECK_QUANTITY'] = $arResult['CHECK_QUANTITY'];
			$arResult['ITEMS'][$index]['CATALOG_QUANTITY'] = CMarketCatalogProduct::GetQuantity($arItem['ID'], $arItem['IBLOCK_ID']);
			
			if ($arResult['ITEMS'][$index]['CHECK_QUANTITY'] && $arResult['ITEMS'][$index]['CATALOG_QUANTITY'] <= 0) {
				$arResult['ITEMS'][$index]['CAN_BUY'] = $arItem['CAN_BUY'] = false;
			}
			$arResult['ITEMS'][$index]['CATALOG_TYPE'] = 1; //simple product
		}
		//end Prices for MARKET
	}
	$arResult['ITEMS'][$index]['ON_REQUEST'] = (empty($arItem['MIN_PRICE']) || $arItem['MIN_PRICE']['VALUE'] <= 0);
	
	if(isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
	{
		$minNotAvailPrice = false;
		$can_buy_find = false;
		$arResult['ITEMS'][$index]['bOffers'] = true;
		$arResult['ITEMS'][$index]['CAN_BUY'] = false;
		$arResult['ITEMS'][$index]['ON_REQUEST'] = false;
		foreach($arItem['OFFERS'] as &$arOffer)
		{
			$minNotAvailPrice = (
				$arOffer['MIN_PRICE']['DISCOUNT_VALUE'] < $minNotAvailPrice['DISCOUNT_VALUE'] || !$minNotAvailPrice
				? $arOffer['MIN_PRICE']
				: $minNotAvailPrice
			);
			$arOffer['ON_REQUEST'] = (empty($arOffer['MIN_PRICE']) || $arOffer['MIN_PRICE']['VALUE'] <= 0);
			if ($arOffer['ON_REQUEST']) {
				$arOffer['CAN_BUY'] = false;
				if(!$arItem['CAN_BUY']) {
					$arResult['ITEMS'][$index]['ON_REQUEST'] = $arOffer['ON_REQUEST'];
				}
			}
			if(!$can_buy_find && $arOffer['CAN_BUY'])
			{
				$arResult['ITEMS'][$index]['CAN_BUY'] = $arItem['CAN_BUY'] = $arOffer['CAN_BUY'];
				if ($arOffer['CATALOG_QUANTITY'] > 0 || $arOffer['CATALOG_QUANTITY_TRACE'] == 'N') {
					$arResult['ITEMS'][$index]['FOR_ORDER'] = $arItem['FOR_ORDER'] = false;
					$can_buy_find = true;
				} else {
					$arResult['ITEMS'][$index]['FOR_ORDER'] = $arItem['FOR_ORDER'] = true;
				}
			}
		}
		unset($arOffer);
		if($arItem['CAN_BUY'])
		{
			$arResult['ITEMS'][$index]['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromOffers(
				$arItem['OFFERS'],
				$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency,
				false
			);
			$arResult['ITEMS'][$index]['ON_REQUEST'] = false;
		}
		else
		{
			$arResult['ITEMS'][$index]['MIN_PRICE'] = $minNotAvailPrice;
		}
	} else {
		// PRICE MATRIX
		if ($arParams["USE_PRICE_COUNT"] && CRZBitronic2Settings::isPro() && $arResult['MODULES']['catalog'] && is_array($arItem['MIN_PRICE'])) {
			$arItem["PRICE_MATRIX"] = CRZBitronic2CatalogUtils::getPriceMatrix($arItem["ID"], $arItem['MIN_PRICE']['PRICE_ID'], $arResult['CONVERT_CURRENCY']);
			$arResult['ITEMS'][$index]['PRICE_MATRIX'] = $arItem['PRICE_MATRIX'];
		}
	}

	if ($arParams['ARTICUL_PROP'] && !empty($arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])) {
		$arResult['NO_ARTICUL'] = false;
	}
}

$cp = $this->__component;
if (is_object($cp)) {
	if ($arResult['NAV_RESULT']->PAGEN >= $arResult['NAV_RESULT']->nEndPage) {
		$iPaginationSelect = $arResult['NAV_RESULT']->NavRecordCount;
	} else {
		$iPaginationSelect = $arResult['NAV_RESULT']->PAGEN * $arResult['NAV_RESULT']->SIZEN;
	}
	$iPaginationCount = $arResult['NAV_RESULT']->NavRecordCount;

	$cp->arResult['NAV_PAGINATION'] = array(
		'NUM' => $arResult['NAV_RESULT']->NavNum,
		'PAGEN' => $arResult['NAV_RESULT']->PAGEN,
		'END_PAGE' => $arResult['NAV_RESULT']->nEndPage,
		'SELECT' => $iPaginationSelect,
		'COUNT' => $arResult['NAV_RESULT']->NavRecordCount,
	);
	$cp->SetResultCacheKeys(array('NAV_PAGINATION'));
}