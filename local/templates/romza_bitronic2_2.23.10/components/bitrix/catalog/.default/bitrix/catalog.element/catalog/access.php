<?
if (
    ((
        is_array($arResult['PROPERTIES']['RECOMMEND']) && 
        !empty($arResult['PROPERTIES']['RECOMMEND']['VALUE']) && 
        count($arResult['PROPERTIES']['RECOMMEND']['VALUE']) > 0
    ) || $arResult['IS_SMARTPHONE_ITEM'] ) && 
    ($canBuy || $arResult['bOffers'])
):
    global $arrAccessFilter;
    if((
        is_array($arResult['PROPERTIES']['RECOMMEND']) && 
        !empty($arResult['PROPERTIES']['RECOMMEND']['VALUE']) && 
        count($arResult['PROPERTIES']['RECOMMEND']['VALUE']) > 0
    ) ){
        $arrAccessFilter = array('ID' => $arResult['PROPERTIES']['RECOMMEND']['VALUE']);
        $bTabAccess = true;
    } else {
        $arrAccessFilter = array('ID' => [0]);
        $bTabAccess = false;
    }
    $templateName = 'sib_detail_list_accessory';

    $sortField = 'PROPERTYSORT_RZ_AVAILABLE';
    if(\Bitrix\Main\Loader::includeModule('sib.core')) {
        $sortField = \Sib\Core\Catalog::getSort($_SESSION["VREGIONS_REGION"]["ID"]);
    }
    
    $arParamsBlock = array(
        "BLOCK_TITLE" => "Аксессуары",
        "LINK_TO_ALL" => "#tab_3",
        //"ONCLICK_ACTION" => '$("a[href=\'#tab_3\'], #tab_3_mobile").click();',
        "LINK_TO_ALL_TEXT" => "Все aксессуары",
        "MAIN_BLOCK_ID" => "detail_access",
        "REPLACE_AJAX_ID" => "rbs-ajax-container_access",
        'JS_OBJECT_NAME' => '$tabAccess',
        "QUICK_VIEW_ACCESS" => 'Y',

        "IS_SMARTPHONE_ITEM" => $arResult['IS_SMARTPHONE_ITEM'] ? 'Y' : 'N',

        "SHOW_ALL_WO_SECTION" => "Y",
        "FILTER_NAME" => 'arrAccessFilter',
        "PAGE_ELEMENT_COUNT" => 0,
        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
        "ADD_SECTIONS_CHAIN" => "N",
        "DISPLAY_COMPARE_SOLUTION" => "Y",
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

        "RESIZER_SET_BIG" => 3,

        "ELEMENT_SORT_FIELD" => $sortField,
        "ELEMENT_SORT_ORDER" => 'ASC',
        "ELEMENT_SORT_FIELD2" => 'CATALOG_PRICE_1', //TODO
        "ELEMENT_SORT_ORDER2" => 'ASC',
        "LIST_PRICE_SORT" => "CATALOG_PRICE_1",

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

    );

    if(\Bitronic2\Mobile::isMobile()){
        $arParamsBlock['LINK_TO_ALL'] = '#tab_3_mobile';
        $arParamsBlock['CACHE_MOBILE'] = 'Y';
    }

    $APPLICATION->IncludeComponent('bitrix:catalog.section', $templateName,
        $arParamsBlock,
        $component
    ); ?>
    <script>
       
            $filterAccess = <?=CUtil::PhpToJSObject($arrAccessFilter);?>,
            $paramsAccess = <?=CUtil::PhpToJSObject($arParamsBlock);?>;

            //$(document).ready(function () {
                getBlockToDetail(
                    '<?=$templateName?>',
                    $filterAccess,
                    $paramsAccess,
                    '#<?=$arParamsBlock['MAIN_BLOCK_ID']?>',
                    '#<?=$arParamsBlock['REPLACE_AJAX_ID']?>'
                );        
            //});

            $paramsAccess.MAIN_BLOCK_ID = 'tab_3';
            $paramsAccess.REPLACE_AJAX_ID = 'replace_tab_3';
            $tabAccess = TabAjaxCatalogController({
                ajaxDir: SITE_DIR + 'ajax/sib/service_ajax.php',
                params: $paramsAccess,
                filter: $filterAccess,
                template: 'sib_detail_list_tab',
                tabId: '#tab_3',
                replaceId: '#replace_tab_3'
            });
        
    </script>
<?endif;?>