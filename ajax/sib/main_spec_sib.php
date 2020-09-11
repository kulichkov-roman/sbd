<?
include_once "include_stop_statistic.php";

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

// @var $moduleId
include_once "include_module.php";

include_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php");

/*if (isset($_GET['action']) && $_GET['action'] == 'ADD2BASKET' && $moduleId == 'yenisite.bitronic2lite') {
	include_once $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/ajax/sib/basket_market.php';
	die();
}*/

include_once "include_options.php";

global $rz_main_spec_filter;
$rz_main_spec_filter = array(
    "PROPERTY_CML2_LINK" => false,
    0 => array(
        "LOGIC" => "OR",
        0=> array(
            ">DATE_CREATE" => "24.09.2018"
        ),
        1 => array("!PROPERTY_NEW" => false)
    ),
    1 => array(
        "LOGIC" => "OR",
        "!DETAIL_PICTURE" => false,
        "!PREVIEW_PICTURE" => false,
        "!PROPERTY_MORE_PHOTO"  => false
    )
);

/* if (\Bitrix\Main\Loader::includeModule('sib.core')) {
    if (isset($_GET['action']) && $_GET['action'] == 'ADD2BASKET' && isset($_GET['id']) && $_GET['id'] > 0) {
        $itemId = (int)$_GET['id'];
        if ($itemInf = \Sib\Core\Helper::getItemInf($itemId)) {
            if ($itemInf['IS_SMARTPHONE']) {
                \Sib\Core\Helper::addFreeServices($itemInf['NAME']);
            }
        }
    }
} */

$APPLICATION->IncludeComponent('bitrix:catalog.section', 'sib_spec', $_POST['params'], false);
