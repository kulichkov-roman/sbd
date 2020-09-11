<?
use Bitrix\Main\Loader;
include_once "include_stop_statistic.php";

// for sef of standart bitrix component
$_SERVER["REQUEST_URI"] = !empty($_REQUEST["REQUEST_URI"]) ? $_REQUEST["REQUEST_URI"] : $_SERVER["REQUEST_URI"];
$_SERVER["SCRIPT_NAME"] = !empty($_REQUEST["SCRIPT_NAME"]) ? $_REQUEST["SCRIPT_NAME"] : $_SERVER["SCRIPT_NAME"];

if(isset($_GET['ajax_basket']) || (isset($_POST["rz_ajax"]) && $_POST["rz_ajax"] === "y"))
{
    require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
}else{
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
}
include_once $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php";

require_once "include_module.php";

if (Loader::IncludeModule('yenisite.core')) {
    $arParams = \Yenisite\Core\Ajax::getParams('bitrix:catalog', ($_REQUEST['CUSTOM_CACHE_KEY'] ?:false), CRZBitronic2CatalogUtils::getCatalogPathForUpdate());
}
if(!is_array($arParams) || empty($arParams)) {
    die("[ajax died] loading params");
}

$arParams['IBLOCK_ID'] = 5;
$arParams['SEF_FOLDER'] = '/dict/';
$arParams['IS_SERVICE_VIEW'] = true;
$arParams['CACHE_TIME'] = 0;
$arParams['SERVICE_PRICE'] = $_REQUEST["QUICK_VIEW_SERVICE_PRICE"];
$APPLICATION->IncludeComponent("bitrix:catalog", "", $arParams, false);

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php";