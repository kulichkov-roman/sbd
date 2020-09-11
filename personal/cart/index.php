<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
global $isNewTemplate;
$tmpl = 'big_basket';
if($isNewTemplate){
    $tmpl = 'sib_big_basket';
}
?><?$APPLICATION->IncludeComponent(
    "bitrix:sale.basket.basket",
    $tmpl,
    array(
        "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
        "COLUMNS_LIST" => array(
            -1 => "PROPERTY_RZ_FOR_ORDER_TEXT",
            0 => "NAME",
            1 => "DISCOUNT",
            2 => "PROPS",
            3 => "DELETE",
            4 => "DELAY",
            5 => "PRICE",
            6 => "QUANTITY",
            7 => "SUM",
        ),
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "PATH_TO_ORDER" => "/personal/order/",
        "HIDE_COUPON" => "N",
        "QUANTITY_FLOAT" => "N",
        "PRICE_VAT_SHOW_VALUE" => "Y",
        "SET_TITLE" => "Y",
        "AJAX_OPTION_ADDITIONAL" => "",
        "USE_PREPAYMENT" => "N",
        "ACTION_VARIABLE" => "action",
        "RESIZER_BASKET_PHOTO" => "13",
        "DELIVERY_URL" => "/about/delivery/",
        "OFFERS_PROPS" => array(
            0 => "COLOR_REF",
            1 => "RAM_REF",
        )
    ),
    false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>