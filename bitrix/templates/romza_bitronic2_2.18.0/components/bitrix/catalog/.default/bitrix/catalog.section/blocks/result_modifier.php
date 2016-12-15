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
if (!$boolConvert)
	$strBaseCurrency = $arResult['CURRENCY'] ? CCurrency::GetBaseCurrency() : 'RUB';

$arResult['HAS_FOR_ORDER'] = false;

if (CModule::IncludeModule('yenisite.market')) {
	$arResult['CHECK_QUANTITY'] = (CMarketCatalog::UsesQuantity($arParams['IBLOCK_ID']) == 1);
}

$arParams['USE_PRICE_COUNT'] = ($arParams['USE_PRICE_COUNT_'] === 'Y');

foreach($arResult['ITEMS'] as $index => $arItem)
{
	$arItem = CRZBitronic2CatalogUtils::processItemCommon($arItem);
	$arItem['bFirst'] = $index == 0;
	if(!empty($arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT']))
	{
		$imgAlt = $arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
	}
	else
	{
		$imgAlt = $arItem['NAME'];
	}
	$arItem['PICTURE_PRINT']['ALT'] = $imgAlt;

	// gallery slider
	if($arParams['HIDE_ICON_SLIDER'] != 'Y' && !$arParams['IS_MOBILE'])
	{
		$productSlider = CRZBitronic2CatalogUtils::getElementPictureArray($arItem);
		if (empty($productSlider))
		{
			$productSlider = array(
				0 => 'no_photo'
			);
		}
		else
		{
			foreach($productSlider as $k=>$photoId)
			{
				$productSlider[$k] = CFile::GetFileArray($photoId);
			}
		}
		$arItem['MORE_PHOTO'] = $productSlider;
		$arItem['MORE_PHOTO_COUNT'] = count($productSlider);
		$arItem['SHOW_SLIDER'] = $arItem['MORE_PHOTO_COUNT'] > 1;
		
		$arItem['PICTURE_PRINT']['SRC'] = CResizer2Resize::ResizeGD2($arItem['MORE_PHOTO'][0]['SRC'], $arParams['RESIZER_SECTION']);
	}
	else
	{
		$arItem['PICTURE_PRINT']['SRC'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['ID'], $arParams['RESIZER_SECTION']);
	}
		
	$arItem['CHECK_QUANTITY'] = false;
	if (!isset($arItem['CATALOG_MEASURE_RATIO']))
		$arItem['CATALOG_MEASURE_RATIO'] = 1;
	if (!isset($arItem['CATALOG_QUANTITY']))
		$arItem['CATALOG_QUANTITY'] = 0;
	$arItem['CATALOG_QUANTITY'] = (
		0 < $arItem['CATALOG_QUANTITY'] && is_float($arItem['CATALOG_MEASURE_RATIO'])
		? floatval($arItem['CATALOG_QUANTITY'])
		: intval($arItem['CATALOG_QUANTITY'])
	);
	$arItem['CATALOG'] = false;
	if (!isset($arItem['CATALOG_SUBSCRIPTION']) || 'Y' != $arItem['CATALOG_SUBSCRIPTION'])
		$arItem['CATALOG_SUBSCRIPTION'] = 'N';

	CIBlockPriceTools::getLabel($arItem, $arParams['LABEL_PROP']);

	if ($arResult['MODULES']['catalog'])
	{
		$arItem['CATALOG'] = true;
		if (!isset($arItem['CATALOG_TYPE']))
			$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
		if (
			(CCatalogProduct::TYPE_PRODUCT == $arItem['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arItem['CATALOG_TYPE'])
			&& !empty($arItem['OFFERS'])
		)
		{
			$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
		}
		switch ($arItem['CATALOG_TYPE'])
		{
			case CCatalogProduct::TYPE_SKU:
				break;
			case CCatalogProduct::TYPE_SET:
				$arItem['OFFERS'] = array();
				//no break;
			case CCatalogProduct::TYPE_PRODUCT:
			default:
				$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
				$arItem['FOR_ORDER']      = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'Y' == $arItem['CATALOG_CAN_BUY_ZERO'] && 0 >= $arItem['CATALOG_QUANTITY']);
				break;
		}
	}
	else
	{
		$arItem['CATALOG_TYPE'] = 0;
		$arItem['OFFERS'] = array();

		//Prices for MARKET
		if (CModule::IncludeModule('yenisite.bitronic2lite') && CModule::IncludeModule('yenisite.market')) {
			$prices = CMarketPrice::GetItemPriceValues($arItem['ID'], $arItem['PRICES']);
			if (count($prices) > 0) {
				unset($arItem['PRICES']);
			}
			$minPrice = false;
			foreach ($prices as $k => $pr) {
				$pr = floatval($pr);
				$arItem['PRICES'][$k]['VALUE'] = $pr;
				$arItem['PRICES'][$k]['PRINT_VALUE'] = $pr;
				if ((empty($minPrice) || $minPrice > $pr) && $pr > 0) {
					$minPrice = $pr;
				}
			}
			if ($minPrice !== false) {
				$arItem['MIN_PRICE']['VALUE'] = $minPrice;
				$arItem['MIN_PRICE']['PRINT_VALUE'] = $minPrice;
				$arItem['MIN_PRICE']['DISCOUNT_VALUE'] = $minPrice;
				$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] = $minPrice;
				$arItem['CATALOG_MEASURE_RATIO'] = 1;
				$arItem['CAN_BUY'] = true;
			}
			$arItem['CHECK_QUANTITY'] = $arResult['CHECK_QUANTITY'];
			$arItem['CATALOG_QUANTITY'] = CMarketCatalogProduct::GetQuantity($arItem['ID'], $arItem['IBLOCK_ID']);
			
			if ($arItem['CHECK_QUANTITY'] && $arItem['CATALOG_QUANTITY'] <= 0) {
				$arItem['CAN_BUY'] = false;
			}
			$arItem['CATALOG_TYPE'] = 1; //simple product
		}
		//end Prices for MARKET
	}
	$arItem['ON_REQUEST'] = (empty($arItem['MIN_PRICE']) || $arItem['MIN_PRICE']['VALUE'] <= 0);
	
	// offers
	if(isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
	{
		$minNotAvailPrice = false;
		$can_buy_find = false;
		$arItem['bOffers'] = true;
		$arItem['CAN_BUY'] = false;
		$arItem['ON_REQUEST'] = false;

		CRZBitronic2CatalogUtils::fillSKUMultiPrice($arItem, $arResult['PRICES']);

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
					$arItem['ON_REQUEST'] = $arOffer['ON_REQUEST'];
				}
			}
			if(!$can_buy_find && $arOffer['CAN_BUY'])
			{
				$arItem['CAN_BUY'] = $arOffer['CAN_BUY'];
				if ($arOffer['CATALOG_QUANTITY'] > 0 || $arOffer['CATALOG_QUANTITY_TRACE'] == 'N') {
					$arItem['FOR_ORDER'] = false;
					$can_buy_find = true;
				} else {
					$arItem['FOR_ORDER'] = true;
				}
			}
		}
		if (isset($arOffer)) {
			unset($arOffer);
		}
		if($arItem['CAN_BUY'])
		{
			$arItem['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromOffers(
				$arItem['OFFERS'],
				$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency,
				false
			);
			$arItem['ON_REQUEST'] = false;
		}
		else
		{
			$arItem['MIN_PRICE'] = $minNotAvailPrice;
		}
	} else {
		// PRICE MATRIX
		if ($arParams["USE_PRICE_COUNT"] && CRZBitronic2Settings::isPro() && $arResult['MODULES']['catalog'] && is_array($arItem['MIN_PRICE'])) {
			$arItem["PRICE_MATRIX"] = CRZBitronic2CatalogUtils::getPriceMatrix($arItem["ID"], $arItem['MIN_PRICE']['PRICE_ID'], $arResult['CONVERT_CURRENCY']);
		}
	}

	ob_start();
	$arItem['STICKERS'] = $APPLICATION->IncludeComponent("yenisite:stickers", "section", array(
		"ELEMENT" => $arItem,
		"STICKER_NEW" => $arParams['STICKER_NEW'],
		"STICKER_HIT" => $arParams['STICKER_HIT'],
		"TAB_PROPERTY_NEW" => $arParams['TAB_PROPERTY_NEW'],
		"TAB_PROPERTY_HIT" => $arParams['TAB_PROPERTY_HIT'],
		"TAB_PROPERTY_SALE" => $arParams['TAB_PROPERTY_SALE'],
		"TAB_PROPERTY_BESTSELLER" => $arParams['TAB_PROPERTY_BESTSELLER'],
		"MAIN_SP_ON_AUTO_NEW" => $arParams['MAIN_SP_ON_AUTO_NEW'],
		"SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
		"CUSTOM_STICKERS" => $arItem['PROPERTIES'][iRZProp::STICKERS],
		),
		$this->__component,
		array("HIDE_ICONS"=>"Y")
	);
	$arItem['yenisite:stickers'] = ob_get_clean();

	$arResult['ITEMS'][$index] = $arItem;

	if ($arItem['FOR_ORDER']) {
		$arResult['HAS_FOR_ORDER'] = true;
	}
}

if ($arParams['SHOW_CATCHBUY']) {
	CRZBitronic2CatalogUtils::getCatchbuyInfoList($arResult['ITEMS']);
}

$fotContent = '';
if ($arResult['HAS_FOR_ORDER']) {
	$fotContent = trim( CMain::GetFileContent( $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . "include_areas/catalog/for_order_text.php" ) );
}
$arResult['AVAILABILITY_COMMENTS_ENABLED'] = !empty($fotContent);
unset($fotContent);

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