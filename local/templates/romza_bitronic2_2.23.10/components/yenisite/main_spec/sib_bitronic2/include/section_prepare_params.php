<?
$arTabParams = array_merge($arParams, array(
    "IS_YS_MS" => "Y",
    "FILTER_NAME" => "rz_main_spec_filter",
    "OFFERS_SORT_FIELD" => (!empty($arParams["LIST_PRICE_SORT"])) ? $arParams["LIST_PRICE_SORT"] : $arParams["OFFERS_SORT_FIELD"],
    "OFFERS_SORT_ORDER" => (!empty($arParams["LIST_PRICE_SORT"])) ? "asc" : $arParams["OFFERS_SORT_ORDER"],
    "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
    'STORE_DISPLAY_TYPE' => $arParams['STORE_DISPLAY_TYPE'],
    "USE_PRICE_COUNT" => "N",
    "USE_PRICE_COUNT_" => $rz_b2_options["extended-prices-enabled"],
    "SHOW_STARS" => $rz_b2_options["block_show_stars"],
    "DISPLAY_COMPARE_SOLUTION" => $rz_b2_options["block_show_compare"] == "Y",
    "SHOW_ARTICLE" => $rz_b2_options["block_show_article"],
    "SHOW_COMMENT_COUNT" => $rz_b2_options["block_show_comment_count"],
    "SHOW_GALLERY_THUMB" => $rz_b2_options["block_show_gallery_thumb"],
    "SHOW_BUY_BTN" => $rz_b2_options['block-buy_button'] === 'Y',
    'HOVER-MODE' => $arResult['HOVER-MODE'],

    'HIDE_ITEMS_NOT_AVAILABLE' => $arParamsCatalog['HIDE_ITEMS_NOT_AVAILABLE'],
    'HIDE_ITEMS_ZER_PRICE' => $arParamsCatalog['HIDE_ITEMS_ZER_PRICE'],
    'HIDE_ITEMS_WITHOUT_IMG' => $arParamsCatalog['HIDE_ITEMS_WITHOUT_IMG'],
    'USE_PRODUCT_QUANTITY' => $rz_b2_options['block-quantity']
));