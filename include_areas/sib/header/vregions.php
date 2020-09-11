<?
if(\Bitrix\Main\Loader::includeModule('aristov.vregions'))
{
    $APPLICATION->IncludeComponent(
        "vregions:header.select",
        "sibdroid_city_new",
        Array(
            "ALLOW_OBLAST_FILTER" => "N",
            "CACHE_TIME" => "31536000",
            "CACHE_TYPE" => "Y",
            "COLS_COUNT" => "3",
            "FIXED" => "N",
            "INCLUDE_SESSION_ARRAY_IN_CACHE" => "Y",
            "POPUP_QUESTION_TITLE" => "Мы угадали?",
            "SHOW_ANOTHER_REGION_BTN" => "N",
            "SHOW_POPUP_QUESTION" => "Y",
            "SHOW_SEARCH_FORM" => "Y",
            "SORT_BY1" => "SORT",
            "SORT_BY2" => "ID",
            "SORT_ORDER1" => "ASC",
            "SORT_ORDER2" => "ASC",
            "STRING_BEFORE_REGION_LINK" => "Ваш регион"
        )
    );
}