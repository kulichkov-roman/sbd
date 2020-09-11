<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
// AJAX PATH
\Bitrix\Main\Loader::includeModule('sib.core');

$ajaxPath = SITE_DIR."ajax/sib/main_spec_sib.php";
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
	$strBaseCurrency = $arResult['CURRENCY'] ? CCurrency::GetBaseCurrency() : 'RUB';

$arResult['HAS_FOR_ORDER'] = false;

if (CModule::IncludeModule('yenisite.market')) {
	$arResult['CHECK_QUANTITY'] = (CMarketCatalog::UsesQuantity($arParams['IBLOCK_ID']) == 1);
}

$arParams['USE_PRICE_COUNT'] = ($arParams['USE_PRICE_COUNT_'] === 'Y');
\Bitrix\Main\Loader::includeModule('sib.core');
$hydroEls = \Sib\Core\Helper::getHydrogelElements('back');
if($arParams['IS_SMARTPHONE_ITEM'] === 'Y' && count($hydroEls) > 0){
	$arResult['CUSTOM_ITEMS'][] = [
		'NAME' => 'Гидрогелевое защитное покрытие для смартфона',
		'PRICE' => $hydroEls[0]['PRICES'][0],
		'OLD_PRICE' => $hydroEls[0]['PRICES'][1],
		'PIC' => [
			'WEBP' => CResizer2Resize::ResizeGD2(SITE_TEMPLATE_PATH . '/img/hydrogel.jpg', 54),
			'JPG' => CResizer2Resize::ResizeGD2(SITE_TEMPLATE_PATH . '/img/hydrogel.jpg', 55),
		],
		'STICKERS' => [
			'VALUE' => [
				'NEW_TECH' => 'Инновационная технология',
				'NEW' => 'Новинка',
			],
			'VALUE_XML_ID' => [
				'NEW_TECH' => 'rbs-hydrogel-sticker',
				'NEW' => 'catalog-label_new',
			]
		],
		'AJAX_HREF' => 'ajax/sib/hydrogel.php?type=back'
	];
}/* 
$hydroEls = \Sib\Core\Helper::getHydrogelElements('front');
if($arParams['IS_SMARTPHONE_ITEM'] === 'Y' && count($hydroEls) > 0){
	$arResult['CUSTOM_ITEMS'][] = [
		'NAME' => 'Гидрогелевое покрытие на переднюю панель смартфона',
		'PRICE' => $hydroEls[0]['PRICES'][0],
		'OLD_PRICE' => $hydroEls[0]['PRICES'][1],
		'PIC' => [
			'WEBP' => CResizer2Resize::ResizeGD2(SITE_TEMPLATE_PATH . '/img/hydro_front.jpg', $arParams['RESIZER_SET_BIG']),
			'JPG' => CResizer2Resize::ResizeGD2(SITE_TEMPLATE_PATH . '/img/hydro_front.jpg', 43),
		],
		'STICKERS' => [
			'VALUE' => [
				'NEW_TECH' => 'Инновационная технология',
				'NEW' => 'Новинка',
			],
			'VALUE_XML_ID' => [
				'NEW_TECH' => 'rbs-hydrogel-sticker',
				'NEW' => 'catalog-label_new',
			]
		],
		'AJAX_HREF' => 'ajax/sib/hydrogel.php?type=front'
	];
} */

foreach ($arResult['ITEMS'] as $index => $arItem)
{
	/* if(\Bitrix\Main\Loader::includeModule('sib.core')){
		$arItem['CATALOG_QUANTITY'] = \Sib\Core\Regions::getQty($arItem['ID']);
	} */
	

	$arItem = CRZBitronic2CatalogUtils::processItemCommon($arItem);
	\Sib\Core\Catalog::checkDiscountPrice($arItem);
	$arItem['bFirst'] = $index == 0;
	if (!empty($arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'])) {
		$imgAlt = $arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
	} else {
		$imgAlt = $arItem['NAME'];
	}
	$arItem['PICTURE_PRINT']['ALT'] = $imgAlt;

	

	// gallery slider
	if ($arParams['HIDE_ICON_SLIDER'] != 'Y' && !$arParams['IS_MOBILE']) {
		$productSlider = CRZBitronic2CatalogUtils::getElementPictureArray($arItem);
		if (empty($productSlider)) {
			$productSlider = array(
				0 => 'no_photo'
			);
		} else {
			foreach ($productSlider as $k=>$photoId) {
				$productSlider[$k] = CFile::GetFileArray($photoId);
			}
		}
		$arItem['MORE_PHOTO'] = $productSlider;
		$arItem['MORE_PHOTO_COUNT'] = count($productSlider);
		$arItem['SHOW_SLIDER'] = $arItem['MORE_PHOTO_COUNT'] > 1;

		$arItem['PICTURE_PRINT']['SRC'] = CResizer2Resize::ResizeGD2($arItem['MORE_PHOTO'][0]['SRC'], 54);
		$arItem['PICTURE_PRINT']['SRC_JPG'] = CResizer2Resize::ResizeGD2($arItem['MORE_PHOTO'][0]['SRC'], 55);
	} else {
		$arItem['PICTURE_PRINT']['SRC'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['ID'], 54);
		$arItem['PICTURE_PRINT']['SRC_JPG'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['ID'], 55);
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

    if (CRZBitronic2Settings::isPro() && CRZBitronic2CatalogUtils::checkAvPrFotoForElement($arItem, $arParams)){
        unset ($arResult['ITEMS'][$index]);
        continue;
    }

	// offers
	if (isset($arItem['OFFERS']) && !empty($arItem['OFFERS'])) {
		CRZBitronic2CatalogUtils::fillSKUMultiPrice($arItem, $arResult['PRICES']);
		CRZBitronic2CatalogUtils::fillMinPriceFromOffers(
			$arItem,
			$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency
		);
		if (!$arItem['CAN_BUY'] && $arParams['HIDE_NOTAVAILABLE'] == 'Y') {
			unset($arResult['ITEMS'][$index]);
			continue; // !!!!!
		}
	} else {
		// PRICE MATRIX
		if ($arParams["USE_PRICE_COUNT"] && CRZBitronic2Settings::isPro() && $arResult['MODULES']['catalog'] && is_array($arItem['MIN_PRICE'])) {
			$arItem["PRICE_MATRIX"] = CRZBitronic2CatalogUtils::getPriceMatrix($arItem["ID"], $arItem['MIN_PRICE']['PRICE_ID'], $arResult['CONVERT_CURRENCY']);
		}
	}

	$arResult['ITEMS'][$index] = $arItem;

	if ($arItem['FOR_ORDER']) {
		$arResult['HAS_FOR_ORDER'] = true;
	}
}

$fotContent = '';
if ($arResult['HAS_FOR_ORDER']) {
	$fotContent = trim( CMain::GetFileContent( $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . "include_areas/sib/catalog/for_order_text.php" ) );
}
$arResult['AVAILABILITY_COMMENTS_ENABLED'] = !empty($fotContent);
unset($fotContent);

