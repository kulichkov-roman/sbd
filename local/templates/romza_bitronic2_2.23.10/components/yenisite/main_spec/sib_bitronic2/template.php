<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (method_exists($this, 'setFrameMode')) $this->setFrameMode(true); ?>

<?
use Bitrix\Main\Loader;

global $rz_main_spec_filter;
global $rz_b2_options;
?>

<? foreach ($arResult['TABS'] as $codeTab => $arTab):
    $codeTab = strtolower($codeTab);
    $all = strtolower($arTab['HEADER']);
    $rz_main_spec_filter = $arTab['FILTER'];
    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($rz_main_spec_filter); echo '</pre>';};
    if(is_array($arParams['TAB_FILTER'][strtoupper($codeTab)])){
        $rz_main_spec_filter = array_merge($rz_main_spec_filter,$arParams['TAB_FILTER'][strtoupper($codeTab)]);
    }

    $arParamsCatalog = array();
    if (Loader::IncludeModule('yenisite.core')) {
        $arParamsCatalog = \Yenisite\Core\Ajax::getParams('bitrix:catalog', false, CRZBitronic2CatalogUtils::getCatalogPathForUpdate());
    }

    CRZBitronic2CatalogUtils::setFilterAvPrFoto($rz_main_spec_filter,$arParamsCatalog);
    $arParams['USE_PRODUCT_QUANTITY'] = $rz_b2_options['block-quantity'];
    unset($rz_main_spec_filter['PROPERTY_OFFERS_IN_REGION']);

    $minCount = $arParams['MIN_COUNT'][strtoupper($codeTab)]?:0;
    
    ?>
    
        <?
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "sib_spec",
            array_merge($arParams, array(
                "TAB_HEADER" => $arTab['HEADER'],
                "TAB_ALL" => $arTab['ALL'],
                "IS_YS_MS" => "Y",
                "TAB_BLOCK" => $codeTab,
                "TAB_LINK" => $arParams['LINK_'.strtoupper($codeTab)],
                "FILTER_NAME" => "rz_main_spec_filter",
                "OFFERS_SORT_FIELD" => (!empty($arParams["LIST_PRICE_SORT"])) ? $arParams["LIST_PRICE_SORT"] : $arParams["OFFERS_SORT_FIELD"],
                "OFFERS_SORT_ORDER" => (!empty($arParams["LIST_PRICE_SORT"])) ? "asc" : $arParams["OFFERS_SORT_ORDER"],
                "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                'STORE_DISPLAY_TYPE' => $arParams['STORE_DISPLAY_TYPE'],
                "USE_PRICE_COUNT" => "N",
                "USE_PRICE_COUNT_" => $rz_b2_options["extended-prices-enabled"],
                "SHOW_STARS" => $rz_b2_options["block_show_stars"],
                "DISPLAY_FAVORITE" => Loader::includeModule('yenisite.favorite') && $rz_b2_options["block_show_favorite"] === "Y",
                "DISPLAY_ONECLICK" => Loader::includeModule('yenisite.oneclick') && $rz_b2_options["block_show_oneclick"] === 'Y',
                "DISPLAY_COMPARE_SOLUTION" => $rz_b2_options["block_show_compare"] == "Y",
                "SHOW_ARTICLE" => $rz_b2_options["block_show_article"],
                "SHOW_COMMENT_COUNT" => $rz_b2_options["block_show_comment_count"],
                "SHOW_GALLERY_THUMB" => $rz_b2_options["block_show_gallery_thumb"],
                "SHOW_BUY_BTN" => $rz_b2_options['block-buy_button'] === 'Y',
                'HOVER-MODE' => $arResult['HOVER-MODE'],

                "MIN_COUNT" => $minCount,

                'HIDE_ITEMS_NOT_AVAILABLE' => $arParamsCatalog['HIDE_ITEMS_NOT_AVAILABLE'],
                'HIDE_ITEMS_ZER_PRICE' => $arParamsCatalog['HIDE_ITEMS_ZER_PRICE'],
                'HIDE_ITEMS_WITHOUT_IMG' => $arParamsCatalog['HIDE_ITEMS_WITHOUT_IMG'],
            )),
            $component
        );?>
       
<? endforeach ?>