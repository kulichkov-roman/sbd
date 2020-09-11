<?
use Bitrix\Main\Loader;
include_once "include_stop_statistic.php";
if(isset($_GET['ajax_basket']) || (isset($_POST["rz_ajax"]) && $_POST["rz_ajax"] === "y"))
{
    require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
}else{
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
}

if(is_array($_POST['params']) && is_array($_POST['filter']) && \Bitrix\Main\Loader::includeModule('yenisite.bitronic2')){
    include_once $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php";

    global ${$_POST['params']['FILTER_NAME']};
    ${$_POST['params']['FILTER_NAME']} = $_POST['filter'];
    $_POST['params']['SHOW_ALL_WO_SECTION'] = 'Y';
    $APPLICATION->IncludeComponent('bitrix:catalog.section', $_POST['template'], $_POST['params'], false);
}