<? 
//require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
if (\Bitrix\Main\Loader::IncludeModule('yenisite.ymrs')): ?>
    <? $APPLICATION->IncludeComponent(
        "yenisite:yandex.market_reviews_store",
        "sib_main_page_mobile",
        array(
            "COMPONENT_TEMPLATE" => "sib_main_page_mobile",
            "SHOPID" => "307694",
            "ACCESSTOKEN" => "4dbc3eeb65b3b7a51573ce3604f10be75288f1575a115688e75033fe1ff29696",
            "HEAD" => "Отзывы о нас на Яндекс Маркете",
            "HEAD_SIZE" => "h2",
            "SORT" => "date",
            "HOW" => "desc",
            "GRADE" => "5",
            "COUNT" => "3",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "86400",
            "INCLUDE_JQUERY" => "N",
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "HIDE_PRO" => "N",
            "HIDE_CONTRA" => "N",
            "HIDE_TEXT" => "N",
            "RESIZER_SET" => "39",
            "REVIEWS_COUNT" => "219",
            "SHOP_RATING" => "5",
        ),
        false,
        array(
            "ACTIVE_COMPONENT" => "Y"
        )
    ); ?>
<?endif?>