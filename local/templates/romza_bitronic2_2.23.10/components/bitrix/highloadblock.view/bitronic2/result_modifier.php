<?
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// extract catalog parameters
$arResult['CATALOG_PARAMS'] = array();
$arMainParams = $arParams;

$arParams = array();
if (Loader::IncludeModule('yenisite.core')) {
	$arParams = \Yenisite\Core\Ajax::getParams('bitrix:catalog', false, CRZBitronic2CatalogUtils::getCatalogPathForUpdate());
}
if(!is_array($arParams) || empty($arParams)) {
	$this->__component->AbortResultCache();
}

// @var $arPrepareParams
include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/prepare_params_element.php';

$arResult['CATALOG_PARAMS'] = $arPrepareParams;
$arParams = $arMainParams;
unset($arMainParams);

// prepare links to catalog with filter
if (Loader::IncludeModule('iblock')) {
	$arProp = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array(
			'IBLOCK_ID' => $arResult['CATALOG_PARAMS']['IBLOCK_ID'],
			'CODE' => $arResult['CATALOG_PARAMS']['BRAND_PROP_CODE'],
			'ACTIVE' => 'Y'
		)
	)->Fetch();
	CBitrixComponent::includeComponentClass("bitrix:catalog.smart.filter");
	$smartFilter = new CBitrixCatalogSmartFilter();
	$smartFilter->fillItemValues($arProp, $arResult['row']['UF_XML_ID']);
	$arResult['LINK'] = $arParams["CATALOG_PATH"]
	                  . '?' . $arResult['CATALOG_PARAMS']['FILTER_NAME']
	                  . $arProp['VALUES'][$arResult['row']['UF_XML_ID']]['CONTROL_ID']
	                  . '=Y&amp;set_filter=y&amp;rz_all_elements=y';
}

do {
	if (!Loader::IncludeModule('yenisite.resizer2')) break;
	if (empty($arResult['row']['UF_FILE'])) break;

	$arFile = CFile::GetFileArray($arResult['row']['UF_FILE']);
	$arSet = CResizer2Set::GetById($arParams["RESIZER_SET"]);
	if (intval($arFile['WIDTH']) <= $arSet['w']
	&&  intval($arFile['HEIGHT']) <= $arSet['h']) break;

	$src = CResizer2Resize::ResizeGD2($arFile['SRC'], $arParams["RESIZER_SET"]);
	$arResult['row']['UF_FILE'] = '<img src="' . $src . '" alt="' . $arResult['row']['UF_NAME'] . '" />';
	return;

} while(0);

global $USER_FIELD_MANAGER;
$arResult['row']['UF_FILE'] = $USER_FIELD_MANAGER->getListView($arResult['fields']['UF_FILE'], $arResult['row']['UF_FILE']);

?>
