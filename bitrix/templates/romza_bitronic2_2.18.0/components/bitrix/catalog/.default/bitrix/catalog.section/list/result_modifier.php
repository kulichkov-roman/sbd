<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;
global $rz_b2_options;
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

if (CModule::IncludeModule('yenisite.market')) {
	$arResult['CHECK_QUANTITY'] = (CMarketCatalog::UsesQuantity($arParams['IBLOCK_ID']) == 1);
}

$arParams['USE_PRICE_COUNT'] = ($arParams['USE_PRICE_COUNT_'] === 'Y');

if ('N' != $arParams['PRODUCT_DISPLAY_MODE'])
	$arParams['PRODUCT_DISPLAY_MODE'] = 'Y';

if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
{
	if (!is_array($arParams['OFFER_TREE_PROPS']))
		$arParams['OFFER_TREE_PROPS'] = array($arParams['OFFER_TREE_PROPS']);
	foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
	{
		$value = (string)$value;
		if ('' == $value || '-' == $value)
			unset($arParams['OFFER_TREE_PROPS'][$key]);
	}
	if (empty($arParams['OFFER_TREE_PROPS']) && isset($arParams['OFFERS_CART_PROPERTIES']) && is_array($arParams['OFFERS_CART_PROPERTIES']))
	{
		$arParams['OFFER_TREE_PROPS'] = $arParams['OFFERS_CART_PROPERTIES'];
		foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
		{
			$value = (string)$value;
			if ('' == $value || '-' == $value)
				unset($arParams['OFFER_TREE_PROPS'][$key]);
		}
	}
}
else
{
	$arParams['OFFER_TREE_PROPS'] = array();
}

if ($arParams['ADD_TO_BASKET_ACTION'] != 'BUY')
	$arParams['ADD_TO_BASKET_ACTION'] = 'ADD';
$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_DETAIL'] = trim($arParams['MESS_BTN_DETAIL']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);

$arEmptyPreview = false;
$strEmptyPreview = CResizer2Resize::ResizeGD2('', $arParams["RESIZER_SETS"]['RESIZER_DETAIL_SMALL']);
if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
{
	$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
	if (!empty($arSizes))
	{
		$arEmptyPreview = array(
			'SRC' => $strEmptyPreview,
			'WIDTH' => (int)$arSizes[0],
			'HEIGHT' => (int)$arSizes[1]
		);
	}
	unset($arSizes);
}
unset($strEmptyPreview);

if (!empty($arResult['ITEMS']))
{
	$arSKUPropList = array();
	$arSKUPropIDs = array();
	$arSKUPropKeys = array();
	$boolSKU = false;
	$strBaseCurrency = '';
	$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);

	if ($arResult['MODULES']['catalog'])
	{
		if (!$boolConvert)
			$strBaseCurrency = CCurrency::GetBaseCurrency();

		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
		$boolSKU = !empty($arSKU) && is_array($arSKU);
		if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']) && 'Y' == $arParams['PRODUCT_DISPLAY_MODE'] && $arParams['SHOW_BUY_BTN'])
		{
			$arSKUPropList = CIBlockPriceTools::getTreeProperties(
				$arSKU,
				$arParams['OFFER_TREE_PROPS'],
				array(
					'PICT' => $arEmptyPreview,
					'NAME' => '-'
				)
			);

			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// ATTENTION!!!!!
			///// DELETE this when there are blocks of SKU COLORS in catalog.section:list
			foreach($arSKUPropList as $k => $arProp)
			{
				$arSKUPropList[$k]['SHOW_MODE'] = 'TEXT';
			}


			$arNeedValues = array();
			CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);
			$arSKUPropIDs = array_keys($arSKUPropList);
			if (empty($arSKUPropIDs))
				$arParams['PRODUCT_DISPLAY_MODE'] = 'N';
			else
				$arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);

			$arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];
		}
	}
	$arNewItemsList = array();
	foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$arItem = CRZBitronic2CatalogUtils::processItemCommon($arItem);
		$arItem['bFirst'] = $key == 0;
		if(!empty($arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT']))
		{
			$imgAlt = $arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
		}
		else
		{
			$imgAlt = $arItem['NAME'];
		}
		$arItem['PICTURE_PRINT']['ALT'] = $imgAlt;
		
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

		if ($arItem['CATALOG'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
		{
			$arItem['bOffers'] = true;

			CRZBitronic2CatalogUtils::fillSKUMultiPrice($arItem, $arResult['PRICES']);
			
			if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'] && $arParams['SHOW_BUY_BTN'])
			{
				$arMatrixFields = $arSKUPropKeys;
				$arMatrix = array();

				$arNewOffers = array();
				$boolSKUDisplayProperties = false;
				$arItem['OFFERS_PROP'] = false;
				$arDouble = array();
				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					$arOffer['ID'] = intval($arOffer['ID']);
					if (isset($arDouble[$arOffer['ID']]))
						continue;
					$arRow = array();
					foreach ($arSKUPropIDs as $propkey => $strOneCode)
					{
						$arCell = array(
							'VALUE' => 0,
							'SORT' => PHP_INT_MAX,
							'NA' => true
						);
						if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
						{
							$arMatrixFields[$strOneCode] = true;
							$arCell['NA'] = false;
							if ('directory' == $arSKUPropList[$strOneCode]['USER_TYPE'])
							{
								$intValue = $arSKUPropList[$strOneCode]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
								$arCell['VALUE'] = $intValue;
							}
							elseif ('L' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']);
							}
							elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']);
							}
							$arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
						}
						$arRow[$strOneCode] = $arCell;
					}
					$arMatrix[$keyOffer] = $arRow;

					CIBlockPriceTools::clearProperties($arOffer['DISPLAY_PROPERTIES'], $arParams['OFFER_TREE_PROPS']);

					CIBlockPriceTools::setRatioMinPrice($arOffer, false);
					
					if(!empty($arOffer['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT']))
					{
						$imgAlt = $arOffer['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
					}
					else
					{
						$imgAlt = $arOffer['NAME'];
					}
					$arOffer['PICTURE_PRINT']['ALT'] = $imgAlt;
				
					if($arParams['HIDE_ICON_SLIDER'] != 'Y')
					{
						$offerSlider = CRZBitronic2CatalogUtils::getElementPictureArray($arOffer);
						if (empty($offerSlider))
						{
							$offerSlider = array(
								0 => $arEmptyPreview
							);
						}
						else
						{
							foreach($offerSlider as $k => $photoId)
							{
								$arPhoto = CFile::GetFileArray($photoId);
								$offerSlider[md5($arPhoto['SRC'])] = $arPhoto;
								unset($offerSlider[$k]);
							}
						}
						$arOffer['MORE_PHOTO'] = $offerSlider;
						$arOffer['MORE_PHOTO_COUNT'] = count($offerSlider);
						$arOffer['SHOW_SLIDER'] = $arOffer['MORE_PHOTO_COUNT'] > 1;
						if(!$arItem['SHOW_SLIDER'] && $arOffer['SHOW_SLIDER']) $arItem['SHOW_SLIDER'] = true;

						reset($arOffer['MORE_PHOTO']);
						$firstPhoto = current($arOffer['MORE_PHOTO']);
						$arOffer['PICTURE_PRINT']['SRC'] = CResizer2Resize::ResizeGD2($firstPhoto['SRC'], $arParams['RESIZER_SECTION']);
					}
					else
					{
						$arOffer['PICTURE_PRINT']['SRC'] = CRZBitronic2CatalogUtils::getElementPictureById($arOffer['ID'], $arParams['RESIZER_SECTION']);
					}
					$arOffer['OWNER_PICT'] = empty($arOffer['PICTURE_PRINT']['SRC']);
					if ($arOffer['OWNER_PICT'])
					{
						$arOffer['PICTURE_PRINT'] = $arItem['PICTURE_PRINT'];
					}

					$arOffer['FOR_ORDER'] = ('Y' == $arOffer['CATALOG_QUANTITY_TRACE'] && 'Y' == $arOffer['CATALOG_CAN_BUY_ZERO'] && 0 >= $arOffer['CATALOG_QUANTITY']);
					$arOffer['ON_REQUEST'] = (empty($arOffer['MIN_PRICE']) || $arOffer['MIN_PRICE']['VALUE'] <= 0);
					if ($arOffer['ON_REQUEST']) {
						$arOffer['CAN_BUY'] = false;
					}
					
					$arDouble[$arOffer['ID']] = true;
					$arNewOffers[$keyOffer] = $arOffer;
				}
				$arItem['OFFERS'] = $arNewOffers;

				$arUsedFields = array();
				$arSortFields = array();

				foreach ($arSKUPropIDs as $propkey => $strOneCode)
				{
					$boolExist = $arMatrixFields[$strOneCode];
					foreach ($arMatrix as $keyOffer => $arRow)
					{
						if ($boolExist)
						{
							if (!isset($arItem['OFFERS'][$keyOffer]['TREE']))
								$arItem['OFFERS'][$keyOffer]['TREE'] = array();
							$arItem['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$strOneCode]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
							$arItem['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
							$arUsedFields[$strOneCode] = true;
							$arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;
						}
						else
						{
							unset($arMatrix[$keyOffer][$strOneCode]);
						}
					}
				}
				$arItem['OFFERS_PROP'] = $arUsedFields;
				$arItem['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');

				Collection::sortByColumn($arItem['OFFERS'], $arSortFields);

				$arMatrix = array();
				$intSelected = -1;
				$arItem['MIN_PRICE'] = false;
				$arItem['MIN_BASIS_PRICE'] = false;
				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					if (empty($arItem['MIN_PRICE']))
					{
						if ($arItem['OFFER_ID_SELECTED'] > 0)
							$foundOffer = ($arItem['OFFER_ID_SELECTED'] == $arOffer['ID']);
						else
							$foundOffer = $arOffer['CAN_BUY'];
						if ($foundOffer)
						{
							$intSelected = $keyOffer;
							$arItem['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']);
							$arItem['MIN_BASIS_PRICE'] = $arOffer['MIN_PRICE'];
						}
						unset($foundOffer);
					}
					$arSKUProps = false;
					if (!empty($arOffer['DISPLAY_PROPERTIES']))
					{
						$boolSKUDisplayProperties = true;
						$arSKUProps = array();
						foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp)
						{
							if ('F' == $arOneProp['PROPERTY_TYPE'])
								continue;
							$arSKUProps[] = array(
								'NAME' => $arOneProp['NAME'],
								'VALUE' => $arOneProp['DISPLAY_VALUE']
							);
						}
						unset($arOneProp);
					}
					if(!empty($arOffer['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])) {
						$arOffer['ARTICUL'] = is_array($arOffer['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
						                    ? implode(' / ', $arOffer['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
						                    : $arOffer['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'];
					}
					// PRICE MATRIX
					$arOffer['PRICE_MATRIX'] = false;
					if ($arParams["USE_PRICE_COUNT"] && CRZBitronic2Settings::isPro() && is_array($arOffer['MIN_PRICE'])) {
						$arOffer["PRICE_MATRIX"] = CRZBitronic2CatalogUtils::getPriceMatrix($arOffer["ID"], $arOffer['MIN_PRICE']['PRICE_ID'], $arResult['CONVERT_CURRENCY']);
					}

					$arOneRow = array(
						'ID' => $arOffer['ID'],
						'URL' => str_replace('&amp;', '&', $arOffer['DETAIL_PAGE_URL']),
						'NAME' => $arOffer['~NAME'],
						'TREE' => $arOffer['TREE'],
						'DISPLAY_PROPERTIES' => $arSKUProps,
						'ARTICUL' => $arOffer['ARTICUL'],
						'PRICE' => $arOffer['MIN_PRICE'],
						'BASIS_PRICE' => $arOffer['MIN_PRICE'],
						'PRICE_MATRIX' => $arOffer['PRICE_MATRIX'],
						'SECOND_PICT' => $arOffer['SECOND_PICT'],
						'OWNER_PICT' => $arOffer['OWNER_PICT'],
						'PICTURE_PRINT' => $arOffer['PICTURE_PRINT'],
						'FOR_ORDER' => $arOffer['FOR_ORDER'],
						'ON_REQUEST' => $arOffer['ON_REQUEST'],
						'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
						'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
						'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
						'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
						'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
						'CAN_BUY' => $arOffer['CAN_BUY'],
						'PRICES' => $arOffer['PRICES']
					);
					$arMatrix[$keyOffer] = $arOneRow;
				}
				if (-1 == $intSelected)
					$intSelected = 0;
				
				$arItem['JS_OFFERS'] = $arMatrix;
				$arItem['OFFERS_SELECTED'] = $intSelected;
				$arItem['OFFERS_PROPS_DISPLAY'] = $boolSKUDisplayProperties;
			}

			$arItem['bSkuSimple'] = empty($arItem['OFFERS_PROP']);
			$arItem['bSkuExt'] = !$arItem['bSkuSimple'];
			
			if('Y' != $arParams['PRODUCT_DISPLAY_MODE'] || $arItem['bSkuSimple'])
			{
				$minNotAvailPrice = false;
				$can_buy_find = false;
				$arItem['bOffers'] = true;
				$arItem['CAN_BUY'] = false;
				foreach($arItem['OFFERS'] as $arOffer)
				{
					$minNotAvailPrice = (
						$arOffer['MIN_PRICE']['DISCOUNT_VALUE'] < $minNotAvailPrice['DISCOUNT_VALUE'] || !$minNotAvailPrice
						? $arOffer['MIN_PRICE']
						: $minNotAvailPrice
					);
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
				if($arItem['CAN_BUY'])
				{
					$arItem['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromOffers(
						$arItem['OFFERS'],
						$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency,
						false
					);
				}
				else
				{
					$arItem['MIN_PRICE'] = $minNotAvailPrice;
				}
			}
			if ($arParams['SHOW_CATCHBUY']) {
				CRZBitronic2CatalogUtils::getCatchbuyInfoList($arItem['OFFERS']);
			}
		} else {
			// PRICE MATRIX
			if ($arParams["USE_PRICE_COUNT"] && CRZBitronic2Settings::isPro() && $arResult['MODULES']['catalog'] && is_array($arItem['MIN_PRICE'])) {
				$arItem["PRICE_MATRIX"] = CRZBitronic2CatalogUtils::getPriceMatrix($arItem["ID"], $arItem['MIN_PRICE']['PRICE_ID'], $arResult['CONVERT_CURRENCY']);
			}
		}
		
		// gallery slider
		if($arParams['HIDE_ICON_SLIDER'] != 'Y' && !$arParams['IS_MOBILE'])
		{
			if(!$arItem['bSkuExt'] || $arParams['ADD_PARENT_PHOTO'] == 'Y')
			{
				$productSlider = CRZBitronic2CatalogUtils::getElementPictureArray($arItem, 'MORE_PHOTO', !$arItem['bSkuExt']);
				if (empty($productSlider) && !$arItem['bSkuExt'])
				{
					$productSlider = array(
						0 => $arEmptyPreview
					);
				}
				else
				{
					foreach($productSlider as $k=>$photoId)
					{
						$arPhoto = CFile::GetFileArray($photoId);
						$productSlider[md5($arPhoto['SRC'])] = $arPhoto;
						unset($productSlider[$k]);
					}
				}
				$arItem['MORE_PHOTO'] = $productSlider;
				$arItem['MORE_PHOTO_COUNT'] = count($productSlider);
				$arItem['SHOW_SLIDER'] = $arItem['MORE_PHOTO_COUNT'] > 1;
				reset($arItem['MORE_PHOTO']);
				$firstPhoto = current($arItem['MORE_PHOTO']);
				$arItem['PICTURE_PRINT']['SRC'] = CResizer2Resize::ResizeGD2($firstPhoto['SRC'], $arParams['RESIZER_SECTION']);
			}
			//sku
			if($arItem['bSkuExt'] && $arParams['ADD_PARENT_PHOTO'] == 'Y')
			{
				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					$arOffer['MORE_PHOTO'] = array_merge($arOffer['MORE_PHOTO'], $arItem['MORE_PHOTO']);
					$arOffer['MORE_PHOTO_COUNT'] = count($arOffer['MORE_PHOTO']);
					$arOffer['SHOW_SLIDER'] = $arOffer['MORE_PHOTO_COUNT'] > 1;
					if(!$arItem['SHOW_SLIDER'] && $arOffer['SHOW_SLIDER']) $arItem['SHOW_SLIDER'] = true;
					
					$arItem['OFFERS'][$keyOffer] = $arOffer;
				}
				unset($arItem['MORE_PHOTO'], $arItem['MORE_PHOTO_COUNT']);
			}
		}
		else
		{
			$arItem['PICTURE_PRINT']['SRC'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['ID'], $arParams['RESIZER_SECTION']);
		}
		
		if (
			$arResult['MODULES']['catalog']
			&& $arItem['CATALOG']
			&&
				($arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT
				|| $arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET)
		)
		{
			CIBlockPriceTools::setRatioMinPrice($arItem, false);
			$arItem['MIN_BASIS_PRICE'] = $arItem['MIN_PRICE'];
		}

		if (!empty($arItem['DISPLAY_PROPERTIES']))
		{
			foreach ($arItem['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
			{
				if ('F' == $arDispProp['PROPERTY_TYPE'])
					unset($arItem['DISPLAY_PROPERTIES'][$propKey]);
			}
		}

		switch ($arParams['REVIEWS_MODE']) {
			case 'blog':  $arItem['REVIEW_COUNT'] = intval($arItem['PROPERTIES']['BLOG_COMMENTS_CNT']['VALUE']); break;
			case 'forum': $arItem['REVIEW_COUNT'] = intval($arItem['PROPERTIES']['FORUM_MESSAGE_CNT']['VALUE']); break;
			case 'feedback':
				$arFilter = array('IBLOCK_ID' => $arParams['FEEDBACK_IBLOCK_ID'], 'PROPERTY_ELEMENT_ID' => $arItem['ID']);
				$arItem['REVIEW_COUNT'] = CIBlockElement::GetList(array(), $arFilter, array());
			default:
				break;
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
			"SKU_EXT" => $arItem['bSkuExt'],
			),
			$this->__component,
			array("HIDE_ICONS"=>"Y")
		);
		$arItem['yenisite:stickers'] = ob_get_clean();

		$arItem['LAST_ELEMENT'] = 'N';
		$arNewItemsList[$key] = $arItem;
	}
	$arNewItemsList[$key]['LAST_ELEMENT'] = 'Y';
	$arResult['ITEMS'] = $arNewItemsList;
	$arResult['SKU_PROPS'] = $arSKUPropList;
	$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;

	$arResult['CURRENCIES'] = array();
	if ($arResult['MODULES']['currency'])
	{
		if ($boolConvert)
		{
			$arResult['CURRENCIES'] = CRZBitronic2CatalogUtils::getCurrencyArray($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
		}
		else
		{
			$arResult['CURRENCIES'] = CRZBitronic2CatalogUtils::getCurrencyArray();
		}
	}
}

if ($arParams['SHOW_CATCHBUY']) {
	CRZBitronic2CatalogUtils::getCatchbuyInfoList($arResult['ITEMS']);
}

/**
 * Кнопка купить в кредит
 */
$arUserItem = array();
$rsUser = \CUser::GetByID($USER->GetID());
$arUserItem = $rsUser->Fetch();

$configuration = \Bitrix\Main\Config\Configuration::getInstance();

$arSection = array();

if(!empty($arResult['ID']))
{
	$obCache = new CPHPCache();
	$cacheLifeTime = 2628000;
	$cacheID = 'arSection'.$arResult['ID'];
	$cachePath = '/yt/'.$cacheID;

	if($obCache->InitCache($cacheLifeTime, $cacheID, $cachePath))
	{
		$vars = $obCache->GetVars();
		$arSection = $vars['arSection'];
	}
	elseif($obCache->StartDataCache())
	{
		$arSectionSort = array();
		$arSectionSelect = array(
			'ID',
			'NAME'
		);
		$arSectionFilter = array(
			'ID' 		=> $arResult['ID'],
			'IBLOCK_ID' => $configuration->get('catalogIBlockId'),
			'ACTIVE'    => 'Y',
		);

		$rsSection = \CIBlockSection::GetList(
			$arSectionSort,
			$arSectionFilter,
			false,
			$arSectionSelect,
			false
		);

		if($arSectionItem = $rsSection->Fetch())
		{
			$arSection = $arSectionItem;
		}
		$obCache->EndDataCache(array('arSection' => $arSection));
	}
}

if(empty($arSection))
{
	$arSection = array(
		'NAME' => ''
	);
}

function getSignMessage1($message, $secretPhrase)
{
	$message = $message.$secretPhrase;
	$result = md5($message).sha1($message);
	for ($i = 0; $i < 1102; $i++)
	{
		$result = md5($result);
	}
	return $result;
}

foreach ($arResult['ITEMS'] as &$arItem)
{
	if($arItem['PRICES']['BASE']['DISCOUNT_VALUE_NOVAT'])
	{
		$price = $arItem['PRICES']['BASE']['DISCOUNT_VALUE_NOVAT'];
	}
	else
	{
		$price = $arItem['PRICES']['BASE']['VALUE'];
	}

	$arOrder = array(
		'items' => array(
			array(
				'title' => $arItem['NAME'],
				'category' => $arItem['NAME'],
				'qty' => 1,
				'price' => $price
			),
		),
		'details' => array(
			'firstname' => $arUserItem['NAME'],
			'lastname' => $arUserItem['LAST_NAME'],
			'middlename' => $arUserItem['SECOND_NAME'],
			'email' => $arUserItem['EMAIL']
		),
		'partnerId' => $configuration->get('partnerId'),
		'partnerOrderId' => $configuration->get('partnerOrderId'),
	);

	$json = json_encode($arOrder);
	$base64 = base64_encode($json);

	$secret = $configuration->get('secretKeyId');

	$sign = getSignMessage1($base64, $secret);

	$arItem['B64_ORDER_PARAMS'] = $base64;
	$arItem['B64_SIGN'] = $sign;
	$arItem['PRICE_CREDIT'] = $price * 13 / 100;
	$arItem['PRICE_CREDIT'] = ceil($arItem['PRICE_CREDIT'] / 100) * 100 - 1;

	$arItem['BUY_CREDIT_SHOW'] = false;
	if($price > IntVal(COption::GetOptionString('askaron.settings', 'UF_CREDIT_MIN_PRICE')))
	{
		$arItem['BUY_CREDIT_SHOW'] = true;
	}
}
unset($arItem);

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
