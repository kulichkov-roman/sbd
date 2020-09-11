<?
$APPLICATION->IncludeComponent(
    "bitrix:menu",
    "sib_footer_catalog",
    array(
        "ROOT_MENU_TYPE" => "footer_catalog",
        "MAX_LEVEL" => "1",
        "CHILD_MENU_TYPE" => "left",
        "USE_EXT" => "N",
        "MENU_CACHE_TYPE" => "A",
        "MENU_CACHE_TIME" => "604800",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "MENU_CACHE_GET_VARS" => array(
        ),
        "TITLE" => "Каталог товаров",
        "COMPONENT_TEMPLATE" => "sib_footer_catalog",
        "DELAY" => "N",
        "CACHE_SELECTED_ITEMS" => false,
        "ALLOW_MULTI_SELECT" => "N"
    ),
    false
);