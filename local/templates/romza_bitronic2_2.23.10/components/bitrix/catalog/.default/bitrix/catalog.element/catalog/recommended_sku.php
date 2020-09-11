<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $recomend_sku;
$recomend_sku['=ID'] = $arResult['FOR_RECOMMEND'];

$APPLICATION->IncludeComponent('bitrix:catalog.section', 'sib_detail_recomend',
    array(
        "FILTER_NAME" => 'recomend_sku',
        "ITEMS" => $arResult['FOR_RECOMMEND'],

        "SHOW_ALL_WO_SECTION" => "Y",
        
        "PAGE_ELEMENT_COUNT" => 6,
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ADD_SECTIONS_CHAIN" => "N",
        "DISPLAY_COMPARE_SOLUTION" => $arParams["USE_COMPARE"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "USE_PRICE_COUNT" => 'N',
        "SHOW_PRICE_COUNT" => '1',
        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
        "PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
        "USE_PRODUCT_QUANTITY" => "N",
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "CACHE_FILTER" => $arParams["CACHE_FILTER"],

       /*  "ELEMENT_SORT_FIELD" => $sortField,
        "ELEMENT_SORT_ORDER" => 'ASC',
        "ELEMENT_SORT_FIELD2" => 'ID', //TODO
        "ELEMENT_SORT_ORDER2" => 'DESC',
        "LIST_PRICE_SORT" => "CATALOG_PRICE_1", */

        "SECTION_ID" => 0,
        'CONVERT_CURRENCY' => "Y",
        'CURRENCY_ID' => $arParams['CONVERT_CURRENCY'] == 'Y' ? $arParams['CURRENCY_ID'] : $arResult['MIN_PRICE']['CURRENCY'],
        'HIDE_NOT_AVAILABLE' => 'N',

        // paginator:
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_DESC_NUMBERING' => 'N',
        'PAGER_SHOW_ALL' => 'N',
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'N',
        'PAGER_TITLE' => ''
    ),
    $component
);