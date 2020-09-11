<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$arParams['RESIZER_SET'] = intval($arParams['RESIZER_SET']) > 0 ? $arParams['RESIZER_SET'] : 5;
$arResult['baseCurrency'] = CModule::IncludeModule('currency') ? CCurrency::GetBaseCurrency() : 'RUB';

if (CModule::IncludeModule('yenisite.market')) {
	$arResult['CHECK_QUANTITY'] = (CMarketCatalog::UsesQuantity($arParams['IBLOCK_ID']) == 1);
}

$arNewItems = array();

foreach ($arResult['ITEMS'] as $index => $arItem) {
	$arItem = CRZBitronic2CatalogUtils::processItemCommon($arItem);
	
	if(!empty($arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT']))
	{
		$imgAlt = $arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
	}
	else
	{
		$imgAlt = $arItem['NAME'];
	}
	$arItem['PICTURE_PRINT']['ALT'] = $imgAlt;
	$arItem['PICTURE_PRINT']['SRC'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['ID'], 3);
	$arItem['PICTURE_PRINT']['SRC_JPG'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['ID'], 43);
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

	$arItem['FOR_ORDER'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'Y' == $arItem['CATALOG_CAN_BUY_ZERO'] && 0 >= $arItem['CATALOG_QUANTITY']);
	$arItem['ON_REQUEST'] = (empty($arItem['MIN_PRICE']) || $arItem['MIN_PRICE']['VALUE'] <= 0);

    if (CRZBitronic2Settings::isPro() && CRZBitronic2CatalogUtils::checkAvPrFotoForElement($arItem, $arParams)){
        unset ($arResult['ITEMS'][$index]);
        continue;
    }

	$arNewItems[$arItem['ID']] = $arItem;
}

$arResult['ITEMS'] = $arNewItems;

foreach ($arParams['SUBSCRIBE_LIST'] as &$obSubscribe) {
	foreach ($obSubscribe->props as $propCode => $arProp) {
		$obSubscribe->props[$propCode] = CIBlockFormatProperties::GetDisplayValue($obSubscribe->fields, $arProp, '');

		$displayValue = $obSubscribe->props[$propCode]['DISPLAY_VALUE'];
		if (is_array($displayValue)) continue;
		if (strpos($displayValue, '<a href="') === false) continue;

		$obSubscribe->props[$propCode]['DISPLAY_VALUE'] = preg_replace(
			'#(<a href="[^"]+">)[^<]+</a>#',
			'$1'. GetMessage('BITRONIC2_SUBSCRIBE_FOLLOW_LINK') .'</a>',
			$displayValue
		);
	}
}
unset($obSubscribe);

// echo '<pre style="display:none">', var_export($arResult['ITEMS'],1), '</pre>';
