<?
use Bitrix\Main\Loader;
include_once "include_stop_statistic.php";

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
require_once "include_module.php";

if (Loader::IncludeModule('yenisite.core')) {
    $arParams = \Yenisite\Core\Ajax::getParams('bitrix:sale.personal.order.list', ($_REQUEST['CUSTOM_CACHE_KEY'] ?:false), CRZBitronic2CatalogUtils::getCatalogPathForUpdate());
}

$arOrderStatuses = array('N', 'NN', 'DK', 'AC', 'ZP', 'YT', 'LO', 'A', 'D', 'O', 'AS', 'KM', 'KO', 'C', 'B', 'E', 'P', 'Y', 'W', 'Z', 'F');

if(!in_array($_POST['sort_status'], $arOrderStatuses)){
    $_REQUEST['show_all'] = "Y";
}

if ( isset($_POST['sort_status']) )
{
    $statusIndex = array_search($_POST['sort_status'], $arOrderStatuses);
    unset($arOrderStatuses[$statusIndex]);

    $arParams["HISTORIC_STATUSES"] = $arOrderStatuses;
}
else
{
    $arParams["HISTORIC_STATUSES"] = array();
}
$arParams["ORDERS_PER_PAGE"] = 10;
$arParams["NAV_TEMPLATE"] = "sib_default";

$APPLICATION->IncludeComponent("bitrix:sale.personal.order.list", "sib_default", $arParams, false);