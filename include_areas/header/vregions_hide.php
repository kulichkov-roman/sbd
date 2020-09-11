<?
if($_POST['ajax-city-hide'] == 'Y'){
    require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
}

if(\Bitrix\Main\Loader::includeModule('aristov.vregions'))
{
    $APPLICATION->IncludeComponent(
        "vregions:header.select",
        "sibdroid_city_new_hide",
        Array(
            "ALLOW_OBLAST_FILTER" => "N",
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "A",
            "COLS_COUNT" => "3",
            "FIXED" => "N",
            "INCLUDE_SESSION_ARRAY_IN_CACHE" => "Y",
            "POPUP_QUESTION_TITLE" => "Мы угадали?",
            "SHOW_ANOTHER_REGION_BTN" => "N",
            "SHOW_POPUP_QUESTION" => "Y",
            "SHOW_SEARCH_FORM" => "Y",
            "SORT_BY1" => "NAME",
            "SORT_BY2" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_ORDER2" => "ASC",
            "STRING_BEFORE_REGION_LINK" => "Ваш регион"
        )
    );
}