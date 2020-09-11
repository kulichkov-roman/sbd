<?use Bitronic2\Mobile;
$method = 'tel';
$phonePart = explode(')', $_SESSION["VREGIONS_REGION"]["TELEFON"]);
if(count($phonePart) < 2){
    $phonePart = $_SESSION["VREGIONS_REGION"]["TELEFON"];
} else {
    $phonePart = '<strong>' . $phonePart[0] . ')</strong> ' . $phonePart[1];
}
?>
<nav class="main-nav js-nav main-nav_mobile main-nav_index <?//=!$bMainPage?'main-nav_inner':'main-nav_index';?>">
    <div class="rbs-close-mob-btn"></div>
    <ul class="main-nav__list">
        
        <li class="main-nav__item js-nav-item">
            <a class="main-nav__link main-nav__link_phone js-nav-link" itemprop="telephone" href="<?=$method?>:<?echo $_SESSION["VREGIONS_REGION"]["TELEFON"]?>">
                <span class="main-nav__fix">
                    <span class="main-nav__text"><?echo $phonePart?></span>
                </span>
            </a>                                
        </li>
        <li class="main-nav__item js-nav-item">
            <?
                if(\Bitrix\Main\Loader::includeModule('aristov.vregions'))
                {
                    $APPLICATION->IncludeComponent(
                        "vregions:header.select",
                        "sibdroid_city_new_mobile",
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
            ?>
        </li>
        <!-- BEGIN ITEM -->
        <li class="main-nav__item js-nav-item">
            <?
                $APPLICATION->IncludeComponent(
                    "bitrix:menu", 
                    "sib_catalog_mobile", 
                    array(
                        "ROOT_MENU_TYPE" => "catalog",
                        "MAX_LEVEL" => "3",
                        "CHILD_MENU_TYPE" => "top_sub",
                        "CACHE_SELECTED_ITEMS" => false,
                            "USE_EXT" => "Y",
                        "MENU_CACHE_TYPE" => "Y",
                        "MENU_CACHE_TIME" => "31536000",
                        "MENU_CACHE_USE_GROUPS" => "N",
                        "ICON_RESIZER_SET" => "8",
                        "RESIZER_SET" => "3",
                        "PRICE_CODE" => array(
                            0 => "BASE",
                        ),
                        "COMPONENT_TEMPLATE" => "catalog",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N",
                        "HITS_COMPONENT" => "CATALOG",
                        "HITS_TYPE" => "SHOW"
                    ),
                    false
                );
            ?>
        </li>
        <!-- ITEM EOF -->
        <li class="main-nav__item js-nav-item">
            <?
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.compare.list", 
                    "sib_header_mobile", 
                    array(
                        "IBLOCK_TYPE" => "catalog",
                        "IBLOCK_ID" => "6",
                        "AJAX_MODE" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "N",
                        "AJAX_OPTION_HISTORY" => "N",
                        "DETAIL_URL" => "/catalog/#ELEMENT_CODE#.html",
                        "COMPARE_URL" => "/catalog/compare.php",
                        "NAME" => "CATALOG_COMPARE_LIST",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "RESIZER_SET_COMPARE" => "9",
                        "COMPONENT_TEMPLATE" => "header",
                        "SHOW_VOTING" => "N",
                        "ACTION_VARIABLE" => "action",
                        "PRODUCT_ID_VARIABLE" => "id"
                    ),
                    false
                );
            ?>
        </li>
        <li>        
            <?
                $APPLICATION->IncludeComponent(
                    "bitrix:sale.basket.basket.line",  
                    "sib_favorite_mobile", 
                    array(
                        "PATH_TO_BASKET" => SITE_DIR."personal/cart/",
                        "SHOW_NUM_PRODUCTS" => "Y",
                        "SHOW_TOTAL_PRICE" => "Y",
                        "SHOW_EMPTY_VALUES" => "Y",
                        "SHOW_PERSONAL_LINK" => "N",
                        "PATH_TO_PERSONAL" => SITE_DIR."personal/settings.php",
                        "SHOW_AUTHOR" => "N",
                        "PATH_TO_REGISTER" => SITE_DIR."auth/",
                        "PATH_TO_PROFILE" => SITE_DIR."personal/settings.php",
                        "SHOW_PRODUCTS" => "Y",
                        "SHOW_DELAY" => "Y",
                        "SHOW_NOTAVAIL" => "N",
                        "SHOW_SUBSCRIBE" => "N",
                        "SHOW_IMAGE" => "N",
                        "SHOW_PRICE" => "Y",
                        "SHOW_SUMMARY" => "Y",
                        "PATH_TO_ORDER" => SITE_DIR."personal/order/",
                        "POSITION_FIXED" => "N",
                        "RESIZER_BASKET_ICON" => "9"
                    ),
                    false
                );
            ?>
        </li>
        <li style="display:none" id="rbs-viewed-block-mobile" class="main-nav__item js-nav-item">
            <a class="main-nav__link main-nav__link_watch js-nav-link js-nav-height" href="#">
                <span class="main-nav__fix">
                    <span class="main-nav__text">
                        Вы смотрели
                        <span >0</span>
                    </span>
                </span>
            </a>
        </li>
        <?
            $APPLICATION->IncludeComponent(
                "bitrix:menu", 
                "sibdroid_menu_top_mobile", 
                array(
                    "ROOT_MENU_TYPE" => "top_sib",
                    "MAX_LEVEL" => "3",
                    "CHILD_MENU_TYPE" => "top_sub",
                    "USE_EXT" => "N",
                    "MENU_CACHE_TYPE" => "Y",
                    "MENU_CACHE_TIME" => "31536000",
                    "MENU_CACHE_USE_GROUPS" => "N",
                    "MENU_CACHE_GET_VARS" => array(),
                    "DELAY" => "N",
                    "CACHE_SELECTED_ITEMS" => false,
                    "ALLOW_MULTI_SELECT" => "N",
                    "COMPONENT_TEMPLATE" => "static",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO"
                ),
                false
            );
        ?>                                                                                                                                                                           
    </ul>
</nav>