<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if ($arParams['VISIBLE_PROPS_COUNT'] !== 0 && (!isset($arParams['VISIBLE_PROPS_COUNT']) || empty($arParams['VISIBLE_PROPS_COUNT']))) {
	$arParams['VISIBLE_PROPS_COUNT'] = 3;
}

$ob = new \CPHPCache();
$cacheString = md5(SITE_ID);
$cachePath = '/' . SITE_ID . '/CurSiteStores';
if ($ob->InitCache(360000, $cacheString, $cachePath)) {
	$arStores = $ob->GetVars();
} else if ($ob->StartDataCache()) {
	$arStores = array();
	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($cachePath);
	if (Bitrix\Main\Loader::includeModule('catalog')) {
		$rs = CCatalogStore::GetList(array(),
			array(
				'ACTIVE' => 'Y',
				'+SITE_ID' => SITE_ID,
			),
			false, false,
			array('ID')
		);
		while ($ar = $rs->GetNext(false, false)) {
			$CACHE_MANAGER->RegisterTag("store_id_" . $ar['ID']);
			$arStores[md5($ar['ID'])] = 1;
		}
	}
	$CACHE_MANAGER->RegisterTag("store_id_new");
	$CACHE_MANAGER->EndTagCache();
	$ob->EndDataCache($arStores);
}

global $rz_b2_options;
if (!empty($rz_b2_options['GEOIP']['STORES'])) {
	$arGeoStores = array();
	foreach ($rz_b2_options['GEOIP']['STORES'] as $storeId) {
		$hash = md5($storeId);
		if (isset($arStores[$hash])) {
			$arGeoStores[$hash] = 1;
		}
	}
	$arStores = $arGeoStores;
	unset($arGeoStores);
}
if (!empty($rz_b2_options['GEOIP']['ITEM'])) {
	$geoSuffix = '_' . $rz_b2_options['GEOIP']['ITEM']['ID'];
} else {
	$geoSuffix = '';
}

/*RBS_CUSTOM_START*/
if(\Bitrix\Main\Loader::includeModule('sib.core'))
{
	$geoSuffix = \Sib\Core\Catalog::getGeoSiffux($_SESSION["VREGIONS_REGION"]["ID"]);
	$priceSuffix = '_' . \Sib\Core\Prices::getPriceRegion();
}
/*RBS_CUSTOM_END*/

$arMarketPrices = array();
if (CModule::includeModule('yenisite.market')) {
	$rsPrices = CMarketPrice::GetList();
	while ($arPrice = $rsPrices->Fetch()) {
		$arMarketPrices[] = $arPrice['code'];
	}
}

// sort prices on top
$arPrices = array();
$arStickers = array();
$arMinPrice = array();
//RBS_CUSTOM_START
if(isset($arResult['ITEMS']['msk'])){
	$arResult['ITEMS']['msk']['NAME'] = 'Розничная цена';
}
//RBS_CUSTOM_END
foreach ($arResult['ITEMS'] as $key => &$arItem) {
	
	if (in_array($arItem['CODE'], $arMarketPrices)) {
		$arItem['PRICE'] = true;
	}
	if ($arItem['PRICE']) {
		$arPrices[$key] = $arItem;
		unset($arResult['ITEMS'][$key]);
	}
	//combine 4 properties with stickers into one item
	if (in_array($arItem['CODE'], $arParams['STICKERS']) && !empty($arItem['VALUES'])) {
		if (empty($arStickers)) {
			$arStickers[0] = array(
				'CODE' => 'RZ_STICKERS',
				'DISPLAY_TYPE' => 'STICKER',
				'NAME' => GetMessage('BITRONIC2_FILTER_STICKERS_NAME'),
				'VALUES' => array()
			);
		}
		$code = array_search($arItem['CODE'], $arParams['STICKERS']);
		$arValue = reset($arItem['VALUES']);
		$arValue['VALUE'] = GetMessage('BITRONIC2_FILTER_STICKER_'.$code);
		$arValue['STICKER'] = $code;
		switch ($code) {
			case 'NEW':        $arValue['CLASS'] = '<span class="catalog-label catalog-label_new">Новинка</span>';        break;
			case 'HIT':
			case 'HIT_MSK':        $arValue['CLASS'] = '<span class="catalog-label catalog-label_hit">Хит</span>';      break;
			case 'SALE':
			case 'SALE_MSK':       $arValue['CLASS'] = '<span class="catalog-label catalog-label_discount">Скидка</span>';    break;
			case 'BESTSELLER': $arValue['CLASS'] = '<span class="catalog-label catalog-label_rec">Рекомендуем</span>'; break;
			default: break;
		}

		if(strpos($code, 'SALE_') === 0){
			$arValue['CLASS'] = '<span class="catalog-label catalog-label_discount">Скидка</span>';
		}

		if ($code == 'NEW' && $arParams['MAIN_SP_ON_AUTO_NEW'] !== 'N' && intval($arParams['STICKER_NEW']) > 0) {
			//var_export($arItem);
			global ${$arParams['FILTER_NAME']};
			$filterKey = '=PROPERTY_'.$arItem['ID'];
			$bSelected = array_key_exists($filterKey, ${$arParams['FILTER_NAME']});

			if ($bSelected) {
				$filterItem = array($filterKey => ${$arParams['FILTER_NAME']}[$filterKey]);
			} else {
				$filterItem = array('!PROPERTY_'.$arItem['ID'] => false);
			}

			$new_time = date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), time() - intval($arParams['STICKER_NEW']) * 86400);
			$filterItem = array(
				"LOGIC" => "OR",
				$filterItem,
				array('>DATE_CREATE' => $new_time)
			);
			if ($bSelected) {
				${$arParams['FILTER_NAME']}[] = $filterItem;
				unset(${$arParams['FILTER_NAME']}[$filterKey]);
			} else {
				$arFilter = array(
					"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
					"IBLOCK_ACTIVE"=>"Y",
					"ACTIVE"=>"Y",
					"INCLUDE_SUBSECTIONS" => "Y",
				);
				if (intval($arParams['SECTION_ID']) > 0) {
					$arFilter['SECTION_ID'] = $arParams['SECTION_ID'];
				}
				$arFilter[] = $filterItem;
				$count = CIBlockElement::GetList(array(), $arFilter, array(), false);
				$arValue['ELEMENT_COUNT'] = $count;
				unset($count, $arFilter);
			}
		}

		unset($arResult['ITEMS'][$key]);

		if ($code == 'CUSTOM') {
			foreach ($arItem['VALUES'] as $arValue) {
				$arValue['CLASS'] = str_replace('+', ' ', $arValue['URL_ID']);
				$arStickers[0]['VALUES'][] = $arValue;
			}
			continue;
		}
        $arKeys = array_keys($arItem['VALUES']);
		$code = $arKeys[0];
        $arValue['ID'] = $arItem['ID'];
		$arStickers[0]['VALUES'][$code] = $arValue;

		continue;
	}
	if ($arItem['CODE'] == 'STORE_AMOUNT_BOOL') {
		$arStoresVal = array();
		foreach ($arItem['VALUES'] as $vKey => $arVal) {
			if (isset($arStores[$arVal['URL_ID']])) {
				$arStoresVal[$vKey] = $arVal;
			}
		}
		$arItem['VALUES'] = $arStoresVal;
		continue;
	}
	if (strpos($arItem['CODE'], 'RZ_AVAILABLE') === 0) {
		if ($arItem['CODE'] === 'RZ_AVAILABLE' . $geoSuffix) continue;
		unset($arResult['ITEMS'][$key]);
	}
	/*RBS_CUSTOM_START*/
	if (strpos($arItem['CODE'], 'SIB_AVAIL') === 0) {
		$arResult['ITEMS'][$key]['NAME'] = "Наличие";
		if ($arItem['CODE'] === 'SIB_AVAIL' . $geoSuffix) continue;
		unset($arResult['ITEMS'][$key]);
	}
	//SIB_MIN_PRICE
	if (strpos($arItem['CODE'], 'SIB_MIN_PRICE') === 0) {
		$arResult['ITEMS'][$key]['NAME'] = "Розничная цена";
		if ($arItem['CODE'] === 'SIB_MIN_PRICE' . $priceSuffix) {
			$arMinPrice[$key] = $arResult['ITEMS'][$key];
		}
		unset($arResult['ITEMS'][$key]);
	}
	if (strpos($arItem['CODE'], 'SALE') === 0) {
		//if ($arItem['CODE'] === 'SIB_AVAIL' . $geoSuffix) continue;
		unset($arResult['ITEMS'][$key]);
	}
	if (strpos($arItem['CODE'], 'HIT') === 0) {
		//if ($arItem['CODE'] === 'SIB_AVAIL' . $geoSuffix) continue;
		unset($arResult['ITEMS'][$key]);
	}
	if (strpos($arItem['CODE'], 'NEW') === 0) {
		//if ($arItem['CODE'] === 'SIB_AVAIL' . $geoSuffix) continue;
		unset($arResult['ITEMS'][$key]);
	}	
	/*RBS_CUSTOM_END*/
}
unset($arItem);

$arResult['ITEMS'] = array_merge(/* $arPrices, */ $arMinPrice, $arStickers, $arResult['ITEMS']);

if($arParams['HIDE_DISABLED_PROPS']) {
	foreach ($arResult['ITEMS'] as &$arItem) {
		$arItem['HIDDEN'] = true;
		foreach ($arItem['VALUES'] as $arValue) {
			if (!$arValue['DISABLED'] || $arValue['CHECKED']) {
				$arItem['HIDDEN'] = false;
				break;
			}
		}
	}
	if (isset($arItem)) {
		unset($arItem);
	}
}

if (\Bitrix\Main\Loader::includeModule('yenisite.seofilter')) {
	CRZSeoFilter::init($this);
}
$debug = 1;
/* foreach ($arResult['ITEMS'] as $key => $arItem){
	if ((isset($arItem['NAME'])) and (strpos($arItem['NAME'], "Наличие") !== false)){
		$arResult['ITEMS'][$key]['NAME'] = "Наличие";
		break;
	}
} */
if(isset($arResult["DISPLAY_TYPE"])){
	$arResult["DISPLAY_TYPE"] = "G";
}
