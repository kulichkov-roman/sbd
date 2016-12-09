<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
use Bitrix\Main\Type\Collection;
use Bitrix\Iblock;

// AJAX PATH
$ajaxPath = SITE_DIR."ajax/catalog.php";
$ajaxPathCompare = SITE_DIR."ajax/compare.php";
$ajaxPathFavorite = SITE_DIR."ajax/favorites.php";
$arResult['ADD_URL_TEMPLATE'] = $ajaxPath."?".$arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#";
// $arResult['FAVORITE_URL'] = $ajaxPath."?".$arParams["ACTION_VARIABLE"]."=SUBSCRIBE_PRODUCT&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arResult["ID"].'&ajax_basket=Y';
$arResult['COMPARE_URL_TEMPLATE'] = $ajaxPathCompare."?".$arParams["ACTION_VARIABLE"]."=ADD_TO_COMPARE_LIST&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";
$arResult['COMPARE_URL_TEMPLATE_DEL'] = $ajaxPathCompare."?".$arParams["ACTION_VARIABLE"]."=DELETE_FROM_COMPARE_LIST&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#&ajax_basket=Y";

$arResult['FAVORITE_URL_TEMPLATE'] = $ajaxPathFavorite."?ACTION=ADD&ID=#ID#";
$arResult['FAVORITE_URL_TEMPLATE_DEL'] = $ajaxPathFavorite."?ACTION=DELETE&ID=#ID#";

$arResult["DISPLAY_UPDATE_DATE"] = CIBlockFormatProperties::DateFormat("d.m.Y", MakeTimeStamp($arResult["TIMESTAMP_X"], CSite::GetDateFormat()));

$arResult['bTechTab'] = (strlen($arResult['DETAIL_TEXT'])>0) 
												|| ($arResult['CATALOG_WEIGHT'] > 0)
												|| (!empty($arResult['DISPLAY_PROPERTIES']));											

$displayPreviewTextMode = array(
	'H' => true,
	'E' => true,
	'S' => true
);
$detailPictMode = array(
	'IMG' => true,
	'POPUP' => true,
	'MAGNIFIER' => true,
	'GALLERY' => true
);

$arDefaultParams = array(
	'ADD_PICT_PROP' => '-',
	'LABEL_PROP' => '-',
	'OFFER_ADD_PICT_PROP' => 'MORE_PHOTO',
	'OFFER_TREE_PROPS' => array('-'),
	'DISPLAY_NAME' => 'Y',
	'DETAIL_PICTURE_MODE' => 'IMG',
	'ADD_DETAIL_TO_SLIDER' => 'Y',
	'DISPLAY_PREVIEW_TEXT_MODE' => 'E',
	'PRODUCT_SUBSCRIPTION' => 'N',
	'SHOW_DISCOUNT_PERCENT' => 'N',
	'SHOW_OLD_PRICE' => 'N',
	'SHOW_MAX_QUANTITY' => 'N',
	'SHOW_BASIS_PRICE' => 'N',
	'ADD_TO_BASKET_ACTION' => array('BUY'),
	'SHOW_CLOSE_POPUP' => 'N',
	'MESS_BTN_BUY' => '',
	'MESS_BTN_ADD_TO_BASKET' => '',
	'MESS_BTN_SUBSCRIBE' => '',
	'MESS_BTN_COMPARE' => '',
	'MESS_NOT_AVAILABLE' => '',
	'USE_VOTE_RATING' => 'N',
	'VOTE_DISPLAY_AS_RATING' => 'rating',
	'USE_COMMENTS' => 'N',
	'BLOG_USE' => 'N',
	'BLOG_URL' => 'catalog_comments',
	'BLOG_EMAIL_NOTIFY' => 'N',
	'VK_USE' => 'N',
	'VK_API_ID' => '',
	'FB_USE' => 'N',
	'FB_APP_ID' => '',
	'BRAND_USE' => 'N',
	'BRAND_PROP_CODE' => ''
);
$arParams = array_merge($arDefaultParams, $arParams);

$arParams['ADD_PICT_PROP'] = trim($arParams['ADD_PICT_PROP']);
if ('-' == $arParams['ADD_PICT_PROP'])
	$arParams['ADD_PICT_PROP'] = '';
$arParams['LABEL_PROP'] = trim($arParams['LABEL_PROP']);
if ('-' == $arParams['LABEL_PROP'])
	$arParams['LABEL_PROP'] = '';
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ('-' == $arParams['OFFER_ADD_PICT_PROP'])
	$arParams['OFFER_ADD_PICT_PROP'] = '';
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
if ('N' != $arParams['DISPLAY_NAME'])
	$arParams['DISPLAY_NAME'] = 'Y';
if (!isset($detailPictMode[$arParams['DETAIL_PICTURE_MODE']]))
	$arParams['DETAIL_PICTURE_MODE'] = 'IMG';
if ('Y' != $arParams['ADD_DETAIL_TO_SLIDER'])
	$arParams['ADD_DETAIL_TO_SLIDER'] = 'N';
if (!isset($displayPreviewTextMode[$arParams['DISPLAY_PREVIEW_TEXT_MODE']]))
	$arParams['DISPLAY_PREVIEW_TEXT_MODE'] = 'E';
if ('Y' != $arParams['PRODUCT_SUBSCRIPTION'])
	$arParams['PRODUCT_SUBSCRIPTION'] = 'N';
if ('Y' != $arParams['SHOW_DISCOUNT_PERCENT'])
	$arParams['SHOW_DISCOUNT_PERCENT'] = 'N';
if ('Y' != $arParams['SHOW_OLD_PRICE'])
	$arParams['SHOW_OLD_PRICE'] = 'N';
if ('Y' != $arParams['SHOW_MAX_QUANTITY'])
	$arParams['SHOW_MAX_QUANTITY'] = 'N';
if ($arParams['SHOW_BASIS_PRICE'] != 'Y')
	$arParams['SHOW_BASIS_PRICE'] = 'N';
if (!is_array($arParams['ADD_TO_BASKET_ACTION']))
	$arParams['ADD_TO_BASKET_ACTION'] = array($arParams['ADD_TO_BASKET_ACTION']);
$arParams['ADD_TO_BASKET_ACTION'] = array_filter($arParams['ADD_TO_BASKET_ACTION'], 'CIBlockParameters::checkParamValues');
if (empty($arParams['ADD_TO_BASKET_ACTION']) || (!in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']) && !in_array('BUY', $arParams['ADD_TO_BASKET_ACTION'])))
	$arParams['ADD_TO_BASKET_ACTION'] = array('BUY');
if ($arParams['SHOW_CLOSE_POPUP'] != 'Y')
	$arParams['SHOW_CLOSE_POPUP'] = 'N';

$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
if ('Y' != $arParams['USE_VOTE_RATING'])
	$arParams['USE_VOTE_RATING'] = 'N';
if ('vote_avg' != $arParams['VOTE_DISPLAY_AS_RATING'])
	$arParams['VOTE_DISPLAY_AS_RATING'] = 'rating';
if ('Y' != $arParams['USE_COMMENTS'])
	$arParams['USE_COMMENTS'] = 'N';
if ('Y' != $arParams['BLOG_USE'])
	$arParams['BLOG_USE'] = 'N';
if ('Y' != $arParams['VK_USE'])
	$arParams['VK_USE'] = 'N';
if ('Y' != $arParams['FB_USE'])
	$arParams['FB_USE'] = 'N';
if ('Y' == $arParams['USE_COMMENTS'])
{
	if ('N' == $arParams['BLOG_USE'] && 'N' == $arParams['VK_USE'] && 'N' == $arParams['FB_USE'])
		$arParams['USE_COMMENTS'] = 'N';
}
if ('Y' != $arParams['BRAND_USE'])
	$arParams['BRAND_USE'] = 'N';

$arParams['QUICK_VIEW'] = $arParams['QUICK_VIEW'] === 'Y' ? true : false;

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

	if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']))
	{
		$arSKUPropList = CIBlockPriceTools::getTreeProperties(
			$arSKU,
			$arParams['OFFER_TREE_PROPS'],
			array(
				'PICT' => $arEmptyPreview,
				'NAME' => '-'
			)
		);
		$arSKUPropIDs = array_keys($arSKUPropList);
	}
}

$arResult['CHECK_QUANTITY'] = false;
if (!isset($arResult['CATALOG_MEASURE_RATIO']))
	$arResult['CATALOG_MEASURE_RATIO'] = 1;
if (!isset($arResult['CATALOG_QUANTITY']))
	$arResult['CATALOG_QUANTITY'] = 0;
$arResult['CATALOG_QUANTITY'] = (
	0 < $arResult['CATALOG_QUANTITY'] && is_float($arResult['CATALOG_MEASURE_RATIO'])
	? (float)$arResult['CATALOG_QUANTITY']
	: (int)$arResult['CATALOG_QUANTITY']
);
$arResult['CATALOG'] = false;
if (!isset($arResult['CATALOG_SUBSCRIPTION']) || 'Y' != $arResult['CATALOG_SUBSCRIPTION'])
	$arResult['CATALOG_SUBSCRIPTION'] = 'N';

CIBlockPriceTools::getLabel($arResult, $arParams['LABEL_PROP']);

$productSlider = CRZBitronic2CatalogUtils::getElementPictureArray($arResult);
if (empty($productSlider))
{
	$productSlider = array(
		0 => $arEmptyPreview
	);
}
else
{
	foreach($productSlider as &$photoId)
	{
		$photoId = CFile::GetFileArray($photoId);
	}
	unset($photoId);
}
$productSliderCount = count($productSlider);
$arResult['SHOW_SLIDER'] = true;
$arResult['MORE_PHOTO'] = $productSlider;
$arResult['MORE_PHOTO_COUNT'] = count($productSlider);

if ($arResult['MODULES']['catalog'])
{
	$arResult['CATALOG'] = true;
	if (!isset($arResult['CATALOG_TYPE']))
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
	if (
		(CCatalogProduct::TYPE_PRODUCT == $arResult['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arResult['CATALOG_TYPE'])
		&& !empty($arResult['OFFERS'])
	)
	{
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
	}
	switch ($arResult['CATALOG_TYPE'])
	{
		case CCatalogProduct::TYPE_SKU:
			break;
		case CCatalogProduct::TYPE_SET:
			$arResult['OFFERS'] = array();
			//no break
		case CCatalogProduct::TYPE_PRODUCT:
		default:
			$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
			$arResult['FOR_ORDER']      = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'Y' == $arResult['CATALOG_CAN_BUY_ZERO'] && 0 >= $arResult['CATALOG_QUANTITY']);
			break;
	}
}
else
{
	$arResult['CATALOG_TYPE'] = 0;
	$arResult['OFFERS'] = array();
}

if ($arResult['CATALOG'] && isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
{
	$arResult['bOffers'] = true;
	$arResult['OFFERS_DISPLAY_PROPS'] = array();
	$boolSKUDisplayProps = false;

	$arResultSKUPropIDs = array();
	$arFilterProp = array();
	$arNeedValues = array();
	foreach ($arResult['OFFERS'] as &$arOffer)
	{
		foreach ($arSKUPropIDs as &$strOneCode)
		{
			if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
			{
				$arResultSKUPropIDs[$strOneCode] = true;
				if (!isset($arNeedValues[$arSKUPropList[$strOneCode]['ID']]))
					$arNeedValues[$arSKUPropList[$strOneCode]['ID']] = array();
				$valueId = (
					$arSKUPropList[$strOneCode]['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST
					? $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']
					: $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']
				);
				$arNeedValues[$arSKUPropList[$strOneCode]['ID']][$valueId] = $valueId;
				unset($valueId);
				if (!isset($arFilterProp[$strOneCode]))
					$arFilterProp[$strOneCode] = $arSKUPropList[$strOneCode];
			}
		}
		unset($strOneCode);

		$arOffer['FOR_ORDER'] = ('Y' == $arOffer['CATALOG_QUANTITY_TRACE'] && 'Y' == $arOffer['CATALOG_CAN_BUY_ZERO'] && 0 >= $arOffer['CATALOG_QUANTITY']);
		$arOffer['ON_REQUEST'] = (empty($arOffer['MIN_PRICE']) || $arOffer['MIN_PRICE']['VALUE'] <= 0);
		if ($arOffer['ON_REQUEST']) {
			$arOffer['CAN_BUY'] = false;
		}
	}
	unset($arOffer);

	CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);
	$arSKUPropIDs = array_keys($arSKUPropList);
	$arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);


	$arMatrixFields = $arSKUPropKeys;
	$arMatrix = array();

	$arNewOffers = array();

	$arIDS = array($arResult['ID']);
	$arOfferSet = array();
	$arResult['OFFER_GROUP'] = false;
	$arResult['OFFERS_PROP'] = false;

	$arDouble = array();
	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
	{
		$arOffer['ID'] = (int)$arOffer['ID'];
		if (isset($arDouble[$arOffer['ID']]))
			continue;
		$arIDS[] = $arOffer['ID'];
		$boolSKUDisplayProperties = false;
		$arOffer['OFFER_GROUP'] = false;
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
					$arCell['VALUE'] = (int)$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID'];
				}
				elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
				{
					$arCell['VALUE'] = (int)$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE'];
				}
				$arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
			}
			$arRow[$strOneCode] = $arCell;
		}
		$arMatrix[$keyOffer] = $arRow;

		CIBlockPriceTools::setRatioMinPrice($arOffer, false);

		$arOffer['MORE_PHOTO'] = array();
		$arOffer['MORE_PHOTO_COUNT'] = 0;
		$offerSlider = CRZBitronic2CatalogUtils::getElementPictureArray($arOffer);
		if (empty($offerSlider))
		{
			$offerSlider = array(
				0 => $arEmptyPreview
			);
		}
		else
		{
			foreach($offerSlider as &$photoId)
			{
				$photoId = CFile::GetFileArray($photoId);
			}
			unset($photoId);
		}
		$arOffer['SHOW_SLIDER'] = true;
		foreach($offerSlider as &$arPhoto) {
			$arOffer['MORE_PHOTO'][md5($arPhoto['SRC'])] = $arPhoto;
		} unset($arPhoto);
		if ($arParams['ADD_PARENT_PHOTO'] == 'Y') {
			foreach($arResult['MORE_PHOTO'] as &$arResPhoto) {
				$resHash = md5($arResPhoto['SRC']);
				if(!isset($arOffer['MORE_PHOTO'][$resHash])) {
					$arOffer['MORE_PHOTO'][$resHash] = $arResPhoto;
				}
			}
		}
		$arOffer['MORE_PHOTO_COUNT'] = count($arOffer['MORE_PHOTO']);

		if (CIBlockPriceTools::clearProperties($arOffer['DISPLAY_PROPERTIES'], $arParams['OFFER_TREE_PROPS']))
		{
			$boolSKUDisplayProps = true;
		}

		$arDouble[$arOffer['ID']] = true;
		$arNewOffers[$keyOffer] = $arOffer;
	}
	
	$arResult['OFFERS'] = $arNewOffers;
	$arResult['SHOW_OFFERS_PROPS'] = $boolSKUDisplayProps;

	$arUsedFields = array();
	$arSortFields = array();

	foreach ($arSKUPropIDs as $propkey => $strOneCode)
	{
		$boolExist = $arMatrixFields[$strOneCode];
		foreach ($arMatrix as $keyOffer => $arRow)
		{
			if ($boolExist)
			{
				if (!isset($arResult['OFFERS'][$keyOffer]['TREE']))
					$arResult['OFFERS'][$keyOffer]['TREE'] = array();
				$arResult['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$strOneCode]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
				$arResult['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
				$arUsedFields[$strOneCode] = true;
				$arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;
			}
			else
			{
				unset($arMatrix[$keyOffer][$strOneCode]);
			}
		}
	}
	$arResult['OFFERS_PROP'] = $arUsedFields;
	$arResult['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');

	
	$arResult['bSkuExt'] = $arParams['PRODUCT_DISPLAY_MODE'] == 'Y' && !empty($arResult['OFFERS_PROP']);
	$arResult['bSkuSimple'] = !$arResult['bSkuExt'];
	
	if($arResult['bSkuExt'])
	{
		Collection::sortByColumn($arResult['OFFERS'], $arSortFields);
	}

	$offerSet = array();
	if (!empty($arIDS) && CBXFeatures::IsFeatureEnabled('CatCompleteSet'))
	{
		$offerSet = array_fill_keys($arIDS, false);
		$rsSets = CCatalogProductSet::getList(
			array(),
			array(
				'@OWNER_ID' => $arIDS,
				'=SET_ID' => 0,
				'=TYPE' => CCatalogProductSet::TYPE_GROUP
			),
			false,
			false,
			array('ID', 'OWNER_ID')
		);
		while ($arSet = $rsSets->Fetch())
		{
			$arSet['OWNER_ID'] = (int)$arSet['OWNER_ID'];
			$offerSet[$arSet['OWNER_ID']] = true;
			$arResult['OFFER_GROUP'] = true;
		}
		if ($offerSet[$arResult['ID']])
		{
			foreach ($offerSet as &$setOfferValue)
			{
				if ($setOfferValue === false)
				{
					$setOfferValue = true;
				}
			}
			unset($setOfferValue);
			unset($offerSet[$arResult['ID']]);
		}
		if ($arResult['OFFER_GROUP'])
		{
			$offerSet = array_filter($offerSet);
			$arResult['OFFER_GROUP_VALUES'] = array_keys($offerSet);
		}
	}

	$arMatrix = array();
	$intSelected = -1;
	//$arResult['MIN_PRICE'] = false;
	$arResult['MIN_BASIS_PRICE'] = false;
	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
	{
		if (-1 == $intSelected && ($arOffer['CAN_BUY'] === true || $arOffer['CAN_BUY'] === 'Y'))
		{
			$intSelected = $keyOffer;
		}
		$arSKUProps = false;
		if (!empty($arOffer['DISPLAY_PROPERTIES']))
		{
			$boolSKUDisplayProps = true;
			$arSKUProps = array();
			foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp)
			{
				// for skuSimple
				if(!in_array($arOneProp['CODE'], $arResult['OFFERS_DISPLAY_PROPS']))
				{
					$arResult['OFFERS_DISPLAY_PROPS'][$arOneProp['CODE']] = $arOneProp['NAME'];
				}
				
				if ('F' == $arOneProp['PROPERTY_TYPE'])
					continue;
				$arSKUProps[] = array(
					'NAME' => $arOneProp['NAME'],
					'VALUE' => $arOneProp['DISPLAY_VALUE']
				);
			}
			unset($arOneProp);
		}
		if (isset($arOfferSet[$arOffer['ID']]))
		{
			$arOffer['OFFER_GROUP'] = true;
			$arResult['OFFERS'][$keyOffer]['OFFER_GROUP'] = true;
		}
		if(!empty($arOffer['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])) {
			$arOffer['ARTICUL'] = is_array($arOffer['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
			                    ? implode(' / ', $arOffer['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
			                    : $arOffer['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'];
		}
		reset($arOffer['MORE_PHOTO']);
		$firstPhotoOriginal = current($arOffer['MORE_PHOTO']);
		$firstPhoto['SRC'] = CResizer2Resize::ResizeGD2($firstPhotoOriginal['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_SMALL']);
		$firstPhoto['SRC_BIG'] = CResizer2Resize::ResizeGD2($firstPhotoOriginal['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_BIG']);
		$firstPhoto['SRC_ICON'] = CResizer2Resize::ResizeGD2($firstPhotoOriginal['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_ICON']);
		$firstPhoto['SRC_FLY'] = CResizer2Resize::ResizeGD2($firstPhotoOriginal['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_FLY_BLOCK']);

		$arPrices = array();
		foreach ($arOffer['PRICES'] as $priceCode => $arPrice) {
			if ($arPrice['ID'] != $arOffer['MIN_PRICE']['ID']) {
				$arPrices[$priceCode] = $arPrice;
			}
		}
		$arOneRow = array(
			'ID' => $arOffer['ID'],
			'URL' => $arOffer['DETAIL_PAGE_URL'],
			'NAME' => $arOffer['~NAME'],
			'TREE' => $arOffer['TREE'],
			'ARTICUL' => $arOffer['ARTICUL'],
			'PRICE' => $arOffer['MIN_PRICE'],
			'BASIS_PRICE' => $arOffer['MIN_PRICE'],
			'DISPLAY_PROPERTIES' => $arSKUProps,
			'PREVIEW_PICTURE' => $firstPhoto,
			'PICTURE_PRINT' => $firstPhoto,
			'FOR_ORDER' => $arOffer['FOR_ORDER'],
			'ON_REQUEST' => $arOffer['ON_REQUEST'],
			'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
			'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
			'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
			'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
			'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
			'OFFER_GROUP' => (isset($offerSet[$arOffer['ID']]) && $offerSet[$arOffer['ID']]),
			'CAN_BUY' => $arOffer['CAN_BUY'],
			'SLIDER' => $arOffer['MORE_PHOTO'],
			'SLIDER_COUNT' => $arOffer['MORE_PHOTO_COUNT'],
			'PRICES' => $arPrices
		);
		unset($arPrice, $priceCode, $arPrices);
		$arMatrix[$keyOffer] = $arOneRow;
	}
	if (-1 == $intSelected)
		$intSelected = 0;
	$arResult['JS_OFFERS'] = $arMatrix;
	$arResult['OFFERS_SELECTED'] = $intSelected;

	$arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];
}

if ($arResult['MODULES']['catalog'] && $arResult['CATALOG'])
{
	if($arResult['bSkuSimple'])
	{
		$arResult['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromOffers(
			$arResult['OFFERS'],
			$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency,
			false
		);
	}
	if ($arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT || $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET)
	{
		CIBlockPriceTools::setRatioMinPrice($arResult, false);
		$arResult['MIN_BASIS_PRICE'] = $arResult['MIN_PRICE'];
	}
	if (CBXFeatures::IsFeatureEnabled('CatCompleteSet') && $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT)
	{
		$rsSets = CCatalogProductSet::getList(
			array(),
			array(
				'@OWNER_ID' => $arResult['ID'],
				'=SET_ID' => 0,
				'=TYPE' => CCatalogProductSet::TYPE_GROUP
			),
			false,
			false,
			array('ID', 'OWNER_ID')
		);
		if ($arSet = $rsSets->Fetch())
		{
			$arResult['OFFER_GROUP'] = true;
		}
	}
}

if (!empty($arResult['DISPLAY_PROPERTIES']))
{
	foreach ($arResult['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
	{
		if ('F' == $arDispProp['PROPERTY_TYPE'])
			unset($arResult['DISPLAY_PROPERTIES'][$propKey]);
	}
}

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

if($arResult['bSkuExt'])
{
	
	if(empty($arResult['MIN_PRICE']))
	{
		$arResult['MIN_PRICE']['CURRENCY'] = ($arParams['CONVERT_CURRENCY'] = 'Y') ? $arParams['CURRENCY_ID'] : $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MIN_PRICE']['CURRENCY'];
	}
}

// =========== SMART FILTER LINKS ==============
$smartFilterURLTemplate = CIBlock::ReplaceDetailUrl($arParams['SEF_FILTER_RULE'], $arResult['SECTION'], $server = false, $type = 'S');
$file = $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/components/bitrix/catalog.smart.filter/class.php';
if (file_exists($file)) {
	include_once $file;
	$smartFilterInstalled = true;
	$smartFilter = new CBitrixCatalogSmartFilter();

	$smartFilter->IBLOCK_ID = $arParams['IBLOCK_ID'];
	$smartFilter->SKU_IBLOCK_ID = $boolSKU ? $arSKU['IBLOCK_ID'] : 0;
	$smartFilter->SECTION_ID = $arResult['IBLOCK_SECTION_ID'];
	$smartFilter->FILTER_NAME = $arParams['FILTER_NAME'];
	$smartFilter->SAFE_FILTER_NAME = htmlspecialcharsBx($smartFilter->FILTER_NAME);
	$smartFilter->arResult = array();
	$smartFilter->arResult['ITEMS'] = $smartFilter->getResultItems();

	$bHiddenBrand = false;

	if(!array_key_exists($arParams['BRAND_PROP_CODE'], $arResult['DISPLAY_PROPERTIES'])
	&&  array_key_exists($arParams['BRAND_PROP_CODE'], $arResult['PROPERTIES'])
	&&  array_key_exists($arResult['PROPERTIES'][$arParams['BRAND_PROP_CODE']]['ID'], $smartFilter->arResult['ITEMS'])
	) {
		$arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROP_CODE']] = $arResult['PROPERTIES'][$arParams['BRAND_PROP_CODE']];
		$arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROP_CODE']]['DISPLAY_VALUE'] = $arResult['PROPERTIES'][$arParams['BRAND_PROP_CODE']]['VALUE'];
		$bHiddenBrand = true;
	}

	foreach ($arResult['DISPLAY_PROPERTIES'] as $propKey => $arProp) {
		if (!array_key_exists($arProp['ID'], $smartFilter->arResult['ITEMS'])) continue;

		if (isset($arProp['VALUE'])         && !is_array($arProp['VALUE']))         $arProp['VALUE']         = array($arProp['VALUE']);
		if (isset($arProp['VALUE_ENUM_ID']) && !is_array($arProp['VALUE_ENUM_ID'])) $arProp['VALUE_ENUM_ID'] = array($arProp['VALUE_ENUM_ID']);
		if (isset($arProp['DISPLAY_VALUE']) && !is_array($arProp['DISPLAY_VALUE'])) $arProp['DISPLAY_VALUE'] = array($arProp['DISPLAY_VALUE']);

		$filterProp = &$smartFilter->arResult['ITEMS'][$arProp['ID']];

		foreach ($arProp['DISPLAY_VALUE'] as $valueKey => $displayValue) {
			$fillValue = isset($arProp['VALUE_ENUM_ID']) ? $arProp['VALUE_ENUM_ID'][$valueKey] : $arProp['VALUE'][$valueKey];
			$smartFilter->fillItemValues($smartFilter->arResult['ITEMS'][$arProp['ID']], $fillValue);

			if ($arParams['SEF_MODE'] == 'Y') {
				if ($filterProp['PROPERTY_TYPE'] == 'N' || $filterProp['DISPLAY_TYPE'] == 'U') {
					if ($filterProp["VALUES"]["MIN"]["VALUE"])
						$filterProp["VALUES"]["MIN"]["HTML_VALUE"] = $filterProp["VALUES"]["MIN"]["VALUE"];
					if ($filterProp["VALUES"]["MAX"]["VALUE"])
						$filterProp["VALUES"]["MAX"]["HTML_VALUE"] = $filterProp["VALUES"]["MAX"]["VALUE"];
				} else {
					foreach ($filterProp['VALUES'] as $filterPropKey => $value) {
						$filterProp['VALUES'][$filterPropKey]['CHECKED'] = true;
					}
				}
				$filterURL = $smartFilter->makeSmartUrl($smartFilterURLTemplate, true);
			} else {
				$paramsToAdd = array(
					"set_filter" => "y",
				);
				foreach($filterProp["VALUES"] as $filterPropKey => $ar)
				{
					if ($filterProp['PROPERTY_TYPE'] == 'N' || $filterProp['DISPLAY_TYPE'] == 'U') {
						$paramsToAdd[$ar['CONTROL_NAME']] = $ar['VALUE'];
					} else {
						$paramsToAdd[$ar['CONTROL_NAME']] = $ar['HTML_VALUE'];
					}
				}

				$filterURL = htmlspecialcharsbx(CHTTP::urlAddParams($arResult['SECTION']['SECTION_PAGE_URL'], $paramsToAdd, array(
					"skip_empty" => true,
					"encode" => true,
				)));
			}
			if (!empty($arParams['BRAND_PROP_CODE']) && $filterProp['CODE'] == $arParams['BRAND_PROP_CODE']) {
				//FILL BRANDS IMAGE AND LINK
				foreach ($filterProp['VALUES'] as $arValue) {
					$arResult['BRAND_LOGO'] = array(
						'ALT' => $arValue['VALUE'],
						'IMG' => CFile::ResizeImageGet($arValue["FILE"], array("width"=>120, "height"=>60)),
						'URL' => $filterURL
						);
					break;
				}
				if ($bHiddenBrand) {
					unset($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROP_CODE']]);
					$filterProp['VALUES'] = array();
					continue 2;
				}
			}
			$filterProp['VALUES'] = array();
			$arProp['DISPLAY_VALUE'][$valueKey] = '<a href="' . $filterURL . '" class="link" rel="nofollow"><span class="text">' . $displayValue . '</span></a>';
		}
		$arResult['DISPLAY_PROPERTIES'][$propKey]['DISPLAY_VALUE'] = (count($arProp['DISPLAY_VALUE']) > 1) ? $arProp['DISPLAY_VALUE'] : $arProp['DISPLAY_VALUE'][0];
	}
}
// =============================================
$arResult["PRICES_LANG"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
/*
echo '<pre>',
htmlspecialcharsbx(var_export($arResult['PROPERTIES']['BRANDS_REF'],1)), "\n\n",
htmlspecialcharsbx(var_export($arResult['DISPLAY_PROPERTIES']['BRANDS_REF'],1)), "\n\n",
htmlspecialcharsbx(var_export($smartFilter->arResult['ITEMS'],1)), "\n\n",
'</pre>';
*/
