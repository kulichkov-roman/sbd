<?
include_once "include_stop_statistic.php";
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

include_once "include_module.php";
include_once "include_options.php";
if (!CModule::IncludeModule('yenisite.core')) die('Error');
if (!CModule::IncludeModule('sib.core')) die('Error');
use Yenisite\Core\Tools;
use Yenisite\Core\Ajax;

Tools::encodeAjaxRequest($_POST);
Tools::encodeAjaxRequest($_REQUEST);

$arAccessories = array();
$blockID = CIBlockElement::GetIBlockByID($_POST['item']);
$recommend = CIBlockElement::GetProperty($blockID, $_POST['item'], "sort", "asc", array("CODE" => "RECOMMEND"));
$propRecomendSections = 'TIP_AKSESSUARA_1';
while ($value = $recommend->GetNext())
{
    $arFilter = array('ID' => $value['VALUE'], 'IBLOCK_ID' => $blockID);
    $element = CIBlockElement::GetList(array(), $arFilter, false, false, array('IBLOCK_SECTION_ID', 'ID', 'CATALOG_AVAILABLE', 'NAME', 'PROPERTY_'.$propRecomendSections));

    if ($arElement = $element->GetNext())
    {
        $arElement['QTY'] = \Sib\Core\Regions::getQty($arElement['ID']);
        if ($arElement['QTY'] > 0 && $arElement['PROPERTY_'.$propRecomendSections.'_ENUM_ID'] == $_POST['section'] && $arElement['CATALOG_AVAILABLE'] === 'Y')
            $arAccessories[] = $arElement['ID'];
    }
}
global $arrBasketFilter;
$isSmartphoneItem = '';
if (\Sib\Core\Helper::isSmarPhoneItem($_POST['item']) && $_POST['section'] === 'HYDROGEL') {
    $arrBasketFilter = array("=ID" => [0]);
    $isSmartphoneItem = true;
} else {
    $arrBasketFilter = array("=ID" => $arAccessories);
    $isSmartphoneItem = false;
}



include $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include_areas/sib/basket/accessories.php";