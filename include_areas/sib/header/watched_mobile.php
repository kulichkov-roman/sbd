<?
$APPLICATION->IncludeComponent(
    "bitrix:catalog.viewed.products", 
    "sib_bitronic2_mobile", 
    array(
        "IBLOCK_MODE" => 'single',
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => "6",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "DETAIL_URL" => "/catalog/#ELEMENT_CODE#.html",
        "RESIZER_SET_COMPARE" => "9",
        "COMPONENT_TEMPLATE" => "sib_bitronic2",
        "SHOW_VOTING" => "N",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "HEADER_TEXT" => "Вы смотрели",
        "PAGE_ELEMENT_COUNT" => 10,
        "SHOW_PRODUCTS_6" => 'Y',
        "PRICE_CODE" => $_SESSION["VREGIONS_REGION"]["PRICE_CODE"],
        "RESIZER_SECTION" => 9
    ),
    false
);