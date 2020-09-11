<?
include_once "include_stop_statistic.php";

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
/**
 * @todo close session after fix resizer 3.0 problem with parallel work
 */
//@session_write_close();

// @var $moduleId
include_once "include_module.php";

include_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php");

if (isset($_GET['action']) && $_GET['action'] == 'ADD2BASKET' && $moduleId == 'yenisite.bitronic2lite') {
	include_once $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/ajax/basket_market.php';
	die();
}

include_once "include_options.php";

if (\Bitrix\Main\Loader::includeModule('sib.core')) {
    if (isset($_GET['action']) && $_GET['action'] == 'ADD2BASKET' && isset($_GET['id']) && $_GET['id'] > 0) {
        $itemId = (int)$_GET['id'];
        if ($itemInf = \Sib\Core\Helper::getItemInf($itemId)) {
            if ($itemInf['IS_SMARTPHONE']) {
                \Sib\Core\Helper::addFreeServices($itemInf['NAME']);
            }
        }
    }
}

//show main spec
include $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include_areas/index/main_spec.php";
