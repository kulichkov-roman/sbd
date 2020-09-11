<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;

$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_ORDERS"), $arResult['PATH_TO_ORDERS']);

Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/components/bitrix/sale.personal.section/bitronic2/js/orders.js');
$arOrderStatuses = array('YT');

if ( isset($_REQUEST['ORDERS_STATUS']) && $_REQUEST['ORDERS_STATUS'] !== 'all' )
{
    $arOrderStatuses = array('N', 'NN', 'DK', 'AC', 'ZP', 'YT', 'LO', 'A', 'D', 'O', 'AS', 'KM', 'KO', 'C', 'B', 'E', 'P', 'Y', 'W', 'Z', 'F');
    $statusIndex = array_search($_REQUEST['ORDERS_STATUS'], $arOrderStatuses);
    unset($arOrderStatuses[$statusIndex]);
} else {
    $_REQUEST['show_all'] = 'Y';
}

?>
<h2 class="account-page-title"><?$APPLICATION->ShowTitle(false)?></h2>
<? include $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'personal/left_menu.php' ?>
<div class="personal-account-main personal-account-main_mb">
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:sale.personal.order.list",
        "sib_default",
        array(
            "PATH_TO_DETAIL" => $arResult["PATH_TO_ORDER_DETAIL"],
            "PATH_TO_CANCEL" => $arResult["PATH_TO_ORDER_CANCEL"],
            "PATH_TO_CATALOG" => $arParams["PATH_TO_CATALOG"],
            "PATH_TO_COPY" => $arResult["PATH_TO_ORDER_COPY"],
            "PATH_TO_BASKET" => $arParams["PATH_TO_BASKET"],
            "PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],
            "SAVE_IN_SESSION" => $arParams["SAVE_IN_SESSION"],
            "ORDERS_PER_PAGE" => $arParams["ORDERS_PER_PAGE"],
            "SET_TITLE" => $arParams["SET_TITLE"],
            "ID" => $arResult["VARIABLES"]["ID"],
            "NAV_TEMPLATE" => $arParams["NAV_TEMPLATE"],
            "ACTIVE_DATE_FORMAT" => $arParams["ACTIVE_DATE_FORMAT"],
            "HISTORIC_STATUSES" => $arOrderStatuses,
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "RESTRICT_CHANGE_PAYSYSTEM" => $arParams["ORDER_RESTRICT_CHANGE_PAYSYSTEM"],
            "DEFAULT_SORT" => "ID"
        ),
        $component
    );
    ?>
</div>
