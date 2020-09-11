<?
if (
    is_array($arResult['PROPERTIES']['SERVICE']) && 
    !empty($arResult['PROPERTIES']['SERVICE']['VALUE']) && 
    count($arResult['PROPERTIES']['SERVICE']['VALUE']) > 0 &&
    ($canBuy || $arResult['bOffers'])
):
    global $arrServiceFilter;
    $arrServiceFilter = array('ID' => $arResult['PROPERTIES']['SERVICE']['VALUE']);
    $bTabServices = true;
    $templateName = 'sib_detail_list_services';

    $sortField = 'PROPERTYSORT_RZ_AVAILABLE';
    if(\Bitrix\Main\Loader::includeModule('sib.core')) {
        $sortField = \Sib\Core\Catalog::getSort($_SESSION["VREGIONS_REGION"]["ID"]);
    }
	$SN = CIBlockSection::GetByID($arResult['IBLOCK_SECTION_ID']);
	if($ar_res = $SN->GetNext())
		$SecName = $ar_res['NAME'];
    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arResult['MIN_PRICE']); echo '</pre>';};

    $arParamsBlock = array(
        "BLOCK_TITLE" => "Выберите услуги",
        "LINK_TO_ALL" => "#tab_4",
        //"ONCLICK_ACTION" => '$("a[href=\'#tab_4\'], #tab_4_mobile").click();',
        "LINK_TO_ALL_TEXT" => "Все услуги",
        "MAIN_BLOCK_ID" => "detail_services",
        "REPLACE_AJAX_ID" => "rbs-ajax-container_services",
        'JS_OBJECT_NAME' => '$tabServices',
        "FILTER_NAME" => 'arrServiceFilter',
        "QUICK_VIEW_SERVICE" => 'Y',

        "SHOW_ALL_WO_SECTION" => "Y",
        
        "PAGE_ELEMENT_COUNT" => 0,
        "IBLOCK_TYPE" => 'REFERENCES',
        "IBLOCK_ID" => $arResult['PROPERTIES']['SERVICE']['LINK_IBLOCK_ID'],
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

        "RESIZER_SET_BIG" => 3,

        "GIFT_IS_SMARTPHONE" => $arResult['GIFT_SMARTPHONE']['IS_SMARTPHONE'],

        "ELEMENT_SORT_FIELD" => 'SORT',
        "ELEMENT_SORT_ORDER" => 'ASC',
        "ELEMENT_SORT_FIELD2" => 'ID', //TODO
        "ELEMENT_SORT_ORDER2" => 'ASC',
        "LIST_PRICE_SORT" => "CATALOG_PRICE_1",

        "SECTION_ID" => 0,
        'CONVERT_CURRENCY' => "Y",
        'CURRENCY_ID' => $arParams['CONVERT_CURRENCY'] == 'Y' ? $arParams['CURRENCY_ID'] : $arResult['MIN_PRICE']['CURRENCY'],
        'HIDE_NOT_AVAILABLE' => 'N',
        
        "GOOD_PRICE" => $arResult['MIN_PRICE']['VALUE'],  //  RBS-CUSTOM
        "ATTACHED_GOOD_SECTION_NAME" => $SecName,  //  RBS-CUSTOM

        // paginator:
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_DESC_NUMBERING' => 'N',
        'PAGER_SHOW_ALL' => 'N',
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'N',
        'PAGER_TITLE' => ''
    );

    if(\Bitronic2\Mobile::isMobile()){
        $arParamsBlock['LINK_TO_ALL'] = '#tab_4_mobile';
        $arParamsBlock['CACHE_MOBILE'] = 'Y';
    }

    /* $APPLICATION->IncludeComponent('bitrix:catalog.section', $templateName,
        $arParamsBlock,
        $component
    ); */?>
    <script>
        var $filterServices = <?=CUtil::PhpToJSObject($arrServiceFilter);?>;
        var $paramsServices = <?=CUtil::PhpToJSObject($arParamsBlock);?>;

            /* getBlockToDetail(
                '<?=$templateName?>',
                $filterServices,
                $paramsServices,
                '#<?=$arParamsBlock['MAIN_BLOCK_ID']?>',
                '#<?=$arParamsBlock['REPLACE_AJAX_ID']?>'
            ); */

        $paramsServices.MAIN_BLOCK_ID = 'tab_4';
        $paramsServices.REPLACE_AJAX_ID = 'replace_tab_4';
        var $tabServices = TabAjaxCatalogController({
            ajaxDir: SITE_DIR + 'ajax/sib/service_ajax.php',
            params: $paramsServices,
            filter: $filterServices,
            hideAvailableBlock: true,
            template: 'sib_detail_list_tab',
            tabId: '#tab_4',
            replaceId: '#replace_tab_4'
        });
    </script>
<?endif;?>