<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitronic2\Catalog\CookiesUtils;
use Bitronic2\Mobile;

global $rz_b2_options;
CookiesUtils::setPrefix(SITE_ID.'_');

// view
//$view = CookiesUtils::getView('blocks');
$view = in_array($_REQUEST['view'], ['blocks', 'list'])? htmlspecialchars($_REQUEST['view']) :'blocks';
// Page count
//$page_count = CookiesUtils::getPageCount();
$page_count = 12; //only static
$arPageCount = CookiesUtils::$_arPageCount;
// ---

// sort
//$sort = CookiesUtils::getSort($arParams, NULL, $arParams['DEFAULT_ELEMENT_SORT_BY']);
$sort = in_array($_REQUEST['sort'], ['created', 'name', 'shows', 'price']) ? ['ACTIVE' => htmlspecialchars($_REQUEST['sort']), 'FOR_PARAMS' => htmlspecialchars($_REQUEST['sort'])] : ['ACTIVE' => 'shows', 'FOR_PARAMS' => 'shows'];
if($sort['ACTIVE'] == 'price'){
	$sort['FOR_PARAMS'] = 'PROPERTY_MINIMUM_PRICE';

	if(\Bitrix\Main\Loader::includeModule('sib.core')){
		$sort['FOR_PARAMS'] = 'PROPERTY_SIB_MIN_PRICE' . \Sib\Core\Prices::getMinPriceSortSuffix();
	}
	
}
$arSort = CookiesUtils::$_arSort;
//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($sort); echo '</pre>';};
// by
$arParams['DEFAULT_ELEMENT_SORT_ORDER'] = $arParams['DEFAULT_ELEMENT_SORT_ORDER'] ? : 'DESC';
//$by = CookiesUtils::getSortBy($arParams['DEFAULT_ELEMENT_SORT_ORDER']);
$by = in_array($_REQUEST['by'], ['asc', 'desc']) ? htmlspecialchars($_REQUEST['by']) : 'desc';
//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($by); echo '</pre>';};

//========= SORT BY IBLOCK PROPERTIES =========//
if (is_array($arParams['LIST_SORT_PROPS']) && !empty($arParams['LIST_SORT_PROPS'])) {
	$cacheId = serialize($arParams['LIST_SORT_PROPS']) . $arParams['IBLOCK_ID'];
	$cachePath = '/bitronic2/catalog/sort-props';
	$obCache = new CPHPCache;
	if ($obCache->InitCache($arParams['CACHE_TIME'], $cacheId, $cachePath)) {
		$arPropNames = $obCache->GetVars();
	} else {
		$arPropNames = array();
		$obRes = CIBlockProperty::GetList(array('sort'=>'asc'), array('IBLOCK_ID' => $arParams['IBLOCK_ID']));
		while ($arProp = $obRes->Fetch()) {
			if (!in_array($arProp['CODE'], $arParams['LIST_SORT_PROPS'])) continue;
			$propCode = 'property_' . strtolower($arProp['CODE']);
			$arPropNames[$propCode] = $arProp['NAME'];
		}
		if ($obCache->StartDataCache()) {
			$obCache->EndDataCache($arPropNames);
		}
	}
	global $MESS;
	foreach ($arPropNames as $propCode => $propName) {
		$MESS['BITRONIC2_CATALOG_SORT_BY_'.$propCode] = $propName;
		unset($arPropNames[$propCode]);
		if (in_array($propCode, $arSort)) continue;
		$arSort[] = $propCode;
	}
}
//========= SORT BY IBLOCK PROPERTIES =========//

/*
if (!in_array($sort['ACTIVE'], $arSupSort)) {
	$sort['ACTIVE'] = $arSupSort[array_rand($arSupSort)];
}*/
if (empty($sort) && in_array('property_rating',$arSort)) {
    $sort['ACTIVE'] = 'property_rating';
}

if (empty($sort)) {
    $sort['ACTIVE'] = 'name';
}

if (!in_array($sort['ACTIVE'], $arSort)) {
    $sort['ACTIVE'] = $arSort[array_rand($arSort)];
    $sort['FOR_PARAMS'] = $sort['ACTIVE'];
}

// pages
foreach ($_REQUEST as $k => $v)
{
	if (strpos($k, 'PAGEN_') === 0)
	{
		if ($_REQUEST[$k] > 0)
		{
			$pagen_key = $k;

			if (strpos($_REQUEST[$k], '?') !== false)
			{
				$tmp = explode('?', $_REQUEST[$k]);
				$pagen = htmlspecialchars($tmp[0]);
			}
			else
			{
				$pagen = htmlspecialchars($_REQUEST[$k]);
			}
			
			break;
		}
	}
}

// for right work of composite
\CHTMLPagesCache::setUserPrivateKey(CacheProvider::getCachePrefix(), 0);


$arAjaxParams = array(
	"view" => $view,
	"page_count" => $page_count,
	"sort" => $sort['ACTIVE'],
	"by" => $by,
);
if(isset($pagen_key, $pagen))
{
	$arAjaxParams[$pagen_key] = $pagen;
}
if (array_key_exists('rz_all_elements', $_REQUEST)) {
	$arAjaxParams['rz_all_elements'] = $_REQUEST['rz_all_elements'];
}
if($_REQUEST["rz_ajax"] !== "y")
{	//FOR AJAX
	?><script type="text/javascript">$.extend(RZB2.ajax.params, <?=CUtil::PhpToJSObject($arAjaxParams, false, true)?>);</script><?
}
?>