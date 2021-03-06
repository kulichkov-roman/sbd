<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Спецпредложения");
/*RBS_CUSTOM_START*/
$tabPropertySale = 'SALE';
$tabPropertyHit = 'HIT';
if(\Bitrix\Main\Loader::includeModule('sib.core')){
	\Sib\Core\Regions::updateRegionStores();
	$tabPropertySale = \Sib\Core\Catalog::getTabPropertySale();
	$tabPropertyHit = \Sib\Core\Catalog::getTabPropertyHit();
}
/*RBS_CUSTOM_END*/
global $arrFilter;
$arrFilter["!PROPERTY_DO_NOT_SHOW_IN_REGION"] = array($_SESSION["VREGIONS_REGION"]["ID"]);

$APPLICATION->IncludeComponent(
	"bitrix:catalog", 
	".default", 
	array(
		"CUSTOM_CACHE_KEY" => 'discount',
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "6",
		"HIDE_NOT_AVAILABLE" => "N",
		"TEMPLATE_THEME" => "",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"DETAIL_SHOW_MAX_QUANTITY" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"DETAIL_USE_VOTE_RATING" => "N",
		"DETAIL_USE_COMMENTS" => "Y",
		"DETAIL_BRAND_USE" => "Y",
		"DETAIL_BRAND_PROP_CODE" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SEF_MODE" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"RESIZER_SECTION_VIP" => "4",
		"RESIZER_COMPLECTS" => "33",
		"IBLOCK_REVIEWS_ID" => "20",
		"RESIZER_IMG_STORE" => "30",
		"RESIZER_BANNER_ACTION" => "27",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_SALE_BESTSELLERS" => "Y",
		"USE_FILTER" => "Y",
		"FILTER_VIEW_MODE" => "VERTICAL",
		"USE_REVIEW" => "Y",
		"MESSAGES_PER_PAGE" => "10",
		"USE_CAPTCHA" => "Y",
		"FORUM_ID" => "1",
		"REVIEWS_MODE" => "blog",
		"USE_COMPARE" => "Y",
		"COMPARE_PATH" => SITE_DIR."ajax/sib/compare_sib.php",
		"PRICE_CODE" => $_SESSION["VREGIONS_REGION"]["PRICE_CODE"],
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/personal/cart/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_PRODUCT_QUANTITY" => $rz_b2_options["block-quantity"],
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"SECTION_COUNT_ELEMENTS" => "Y",
		"SECTION_TOP_DEPTH" => "2",
		"SECTIONS_VIEW_MODE" => "LIST",
		"SECTIONS_SHOW_PARENT_NAME" => "Y",
		"PAGE_ELEMENT_COUNT" => "24",
		"LINE_ELEMENT_COUNT" => "3",
		"ELEMENT_SORT_FIELD" => "",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "shows",
		"ELEMENT_SORT_ORDER2" => "asc",
		"LIST_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"SHORT_PROPERTY_DETAIL_LIST" => array(
			"VES_GRAMM",
			"RAZMERY_SHXVXT",
			"TIP_EKRANA",
			"EMKOST_AKKUMULYATORA_MACH",
			"OSOBENNOSTI"
		),
		"INCLUDE_SUBSECTIONS" => "Y",
		"LIST_META_KEYWORDS" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_BROWSER_TITLE" => "-",
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_DISPLAY_NAME" => "Y",
		"DETAIL_DETAIL_PICTURE_MODE" => "IMG",
		"DETAIL_ADD_DETAIL_TO_SLIDER" => "Y",
		"DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "H",
		"LINK_IBLOCK_TYPE" => "catalog",
		"LINK_IBLOCK_ID" => "421",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"USE_ALSO_BUY" => "N",
		"USE_STORE" => "Y",
		"PAGER_TEMPLATE" => "sib_default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"ADD_PICT_PROP" => "-",
		"LABEL_PROP" => "-",
		"PRODUCT_DISPLAY_MODE" => "Y",
		"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
		"OFFER_TREE_PROPS" => array(
		),
		"OFFERS_CART_PROPERTIES" => array(
		),
		"LIST_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_OFFERS_LIMIT" => "0",
		"DETAIL_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_SORT_FIELD" => "shows",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "shows",
		"OFFERS_SORT_ORDER2" => "asc",
		"SEF_FOLDER" => "/discount/",
		"FILTER_GROUP_FEATURES" => "",
		"FILTER_GROUP_LOCALITY" => "",
		"FILTER_GROUP_EXTRA" => "",
		"RESIZER_SECTION" => "3",
		"RESIZER_DETAIL_SMALL" => "15",
		"RESIZER_DETAIL_BIG" => "16",
		"RESIZER_DETAIL_ICON" => "13",
		"AJAX_OPTION_ADDITIONAL" => "",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"LIST_PRICE_SORT" => "CATALOG_PRICE_1",
		"DEFAULT_ELEMENT_SORT_BY" => "SHOWS",
		"DEFAULT_ELEMENT_SORT_ORDER" => "DESC",
		"FILTER_NAME" => "arrFilter",
		"FILTER_FIELD_CODE" => ",",
		"FILTER_PROPERTY_CODE" => ",",
		"FILTER_PRICE_CODE" => "",
		"FILTER_OFFERS_FIELD_CODE" => ",",
		"FILTER_OFFERS_PROPERTY_CODE" => ",",
		"DETAIL_BLOG_USE" => "Y",
		"DETAIL_BLOG_URL" => "feedback",
		"DETAIL_BLOG_EMAIL_NOTIFY" => "N",
		"DETAIL_VK_USE" => "N",
		"DETAIL_FB_USE" => "N",
		"BLOG_EMAIL_NOTIFY" => "Y",
		"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
		"COMPARE_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"COMPARE_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"COMPARE_ELEMENT_SORT_FIELD" => "shows",
		"COMPARE_ELEMENT_SORT_ORDER" => "asc",
		"DISPLAY_ELEMENT_SELECT_BOX" => "N",
		"DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
		"COMPARE_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"COMPARE_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"USE_STORE_PHONE" => "N",
		"USE_STORE_SCHEDULE" => "N",
		"USE_MIN_AMOUNT" => "Y",
		"MIN_AMOUNT" => "1",
		"STORE_PATH" => "/store/#store_id#",
		"MAIN_TITLE" => "Наличие на складах",
		"COMPONENT_TEMPLATE" => ".default",
		"STICKER_NEW" => "1",
		"RESIZER_SECTION_LVL0" => "9",
		"FILTER_VISIBLE_PROPS_COUNT" => "8",
		"DETAIL_FEEDBACK_USE" => "Y",
		"SHOW_TOP_ELEMENTS" => "Y",
		"TOP_ELEMENT_COUNT" => "9",
		"TOP_LINE_ELEMENT_COUNT" => "3",
		"TOP_ELEMENT_SORT_FIELD" => "sort",
		"TOP_ELEMENT_SORT_ORDER" => "asc",
		"TOP_ELEMENT_SORT_FIELD2" => "id",
		"TOP_ELEMENT_SORT_ORDER2" => "desc",
		"TOP_PROPERTY_CODE" => ",",
		"TOP_OFFERS_FIELD_CODE" => ",",
		"TOP_OFFERS_PROPERTY_CODE" => ",",
		"TOP_OFFERS_LIMIT" => "5",
		"HIDE_SHOW_ALL_BUTTON" => "N",
		"STORES" => $_SESSION["VREGIONS_REGION"]["ID_SKLADA"],
		"USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SHOW_EMPTY_STORE" => "Y",
		"SHOW_GENERAL_STORE_INFORMATION" => "N",
		"ARTICUL_PROP" => "VES_GRAMM",
		"FEEDBACK_IBLOCK_TYPE" => "bbs_ads",
		"FEEDBACK_IBLOCK_ID" => "421",
		"RESIZER_DETAIL_PROP" => "21",
		"RESIZER_DETAIL_FLY_BLOCK" => "3",
		"DISPLAY_FAVORITE" => "Y",
		"DISPLAY_ONECLICK" => "Y",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"DETAIL_PROPERTY_CODE" => array('VES_GRAMM'),
		"SET_LAST_MODIFIED" => "N",
		"REVIEW_AJAX_POST" => "",
		"PATH_TO_SMILE" => "",
		"URL_TEMPLATES_READ" => "",
		"SHOW_LINK_TO_FORUM" => "",
		"RESIZER_SECTION_ICON" => "5",
		"RESIZER_COMMENT_AVATAR" => "6",
		"RESIZER_SET_CONTRUCTOR" => "3",
		"RESIZER_RECOMENDED" => "3",
		"RESIZER_FILTER" => "5",
		"TOP_VIEW_MODE" => "",
		"USE_OWN_REVIEW" => "N",
		"DETAIL_YM_API_USE" => "N",
		"HIDE_ICON_SLIDER" => "N",
		"HIDE_STORE_LIST" => "Y",
		"SHOW_DESCRIPTION_TOP" => "N",
		"SHOW_DESCRIPTION_BOTTOM" => "Y",
		"LIST_BRAND_USE" => "Y",
		"LIST_BRAND_PROP_CODE" => "BRANDS_REF",
		"SECTION_SHOW_HITS" => "N",
		"SECTION_HITS_RCM_TYPE" => "bestsell",
		"SECTION_HITS_HIDE_NOT_AVAILABLE" => "Y",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"SHOW_DEACTIVATED" => "N",
		"SETTINGS_HIDE" => array(
			0 => "TSVET",
			1 => "REKOMENDUEYE_TOVARY",
			2 => "GARANTIYA_PROIZVODITELYA",
			3 => "NAZVANIE_YANDEX",
			4 => "INSTRUKTSIYA",
			5 => "TURBO_YANDEX_LINK",
			6 => "UPDATE_OPINIONS",
			7 => "RESP_COUNT",
			8 => "RESP_QUANT",
			9 => "FRONTALNAYA_KAMERA_MP",
			10 => "OSNOVNAYA_KAMERA_MP",
			11 => "GARANTIYA_NA_TOVAR",
			12 => "YANDEX_ID",
			13 => "SALE_MSK",
			14 => "HIT_MSK",
		),
		"DETAIL_HIDE_ACCESSORIES" => "N",
		"DETAIL_HIDE_SIMILAR" => "N",
		"DETAIL_HIDE_SIMILAR_VIEW" => "N",
		"ADD_PARENT_PHOTO" => "N",
		"OFFER_VAR_NAME" => "pid",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"COMPARE_META_H1" => "Что лучше: #TEXT# ?",
		"COMPARE_META_TITLE" => "Что лучше купить: #TEXT# ?",
		"COMPARE_META_KEYWORDS" => "Сравнение #TEXT#",
		"COMPARE_META_DESCRIPTION" => "Сравнение #TEXT#",
		"MANUAL_PROP" => "MANUAL",
		"SECTION_BACKGROUND_IMAGE" => "-",
		"DETAIL_BACKGROUND_IMAGE" => "-",
		"RESIZER_SUBSECTION" => "5",
		"DETAIL_FOUND_CHEAP" => "N",
		"DETAIL_PRICE_LOWER" => "N",
		"ADV_BANNER_TYPE" => "-",
		"MAIN_SP_ON_AUTO_NEW" => "N",
		"TAB_PROPERTY_NEW" => "NEW",
		"TAB_PROPERTY_HIT" => $tabPropertyHit,
		"TAB_PROPERTY_SALE" => $tabPropertySale,
		"TAB_PROPERTY_BESTSELLER" => "BESTSELLER",
		"RESIZER_QUICK_VIEW" => "2",
		"DETAIL_SIMILAR_PRICE_PERCENT" => "20",
		"DETAIL_ACCESSORIES_TITLE" => "Не забудьте добавить к заказу",
		"DETAIL_SIMILAR_TITLE" => "Похожие товары",
		"DETAIL_SIMILAR_VIEW_TITLE" => "Просматриваемые с этим товаром",
		"DETAIL_SIMILAR_PRICE_TITLE" => "Похожие товары",
		"DETAIL_RECOMMENDED_TITLE" => "Рекомендуемые вместе с этим товаром",
		"DETAIL_VIEWED_TITLE" => "Вы смотрели",
		"FILE_404" => "",
		"SLIDERS_HIDE_NOT_AVAILABLE" => "N",
		"BIGDATA_BESTSELL_TITLE" => "Лидеры продаж",
		"BIGDATA_PERSONAL_TITLE" => "Рекомендуем вам",
		"USE_GIFTS_SECTION" => "",
		"GIFTS_SHOW_NAME" => "",
		"GIFTS_SHOW_IMAGE" => "",
		"GIFTS_MESS_BTN_BUY" => "",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"FILTER_DISPLAY_ELEMENT_COUNT" => "Y",
		"FILTER_HIDE_DISABLED_VALUES" => "Y",
		"FILTER_SHOW_NAME_FIELD" => "N",
		"LIST_SORT_PROPS" => array(
		),
		"DETAIL_SIMILAR_PRICE_SMART_FILTER" => "N",
		"DETAIL_SIMILAR_PRICE_WITH_EMPTY_PROPS" => "N",
		"DETAIL_SIMILAR_PRICE_PROPERTIES" => array(
			0 => "---AVAILABLE---",
			1 => "---SECTION---",
			2 => "---PRICE---",
		),
		"BRAND_DETAIL" => SITE_DIR."brands/#ID#/",
		"EDOST_SHOW_QTY" => "Y",
		"EDOST_SHOW_ADD_CART" => "Y",
		"EDOST_MINIMIZE" => "N",
		"EDOST_SORT" => "Y",
		"EDOST_ECONOMIZE" => "Y",
		"EDOST_ECONOMIZE_INLINE" => "Y",
		"EDOST_MAX" => "5",
		"EDOST_PRICE_VALUE" => "min",
		"USE_GIFTS_DETAIL" => "Y",
		"USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
		"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "3",
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "3",
		"GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
		"GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "3",
		"GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DETAIL_SET_VIEWED_IN_COMPONENT" => "Y",
		"PROP_FOR_BANNER" => "-",
		"PROP_FOR_DISCOUNT" => "-",
		"IBLOCK_ACTIONS_TYPE" => "bitronic2_actions",
		"IBLOCK_ACTIONS_ID" => "34",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"HIDE_ITEMS_NOT_AVAILABLE" => "N",
		"HIDE_ITEMS_ZER_PRICE" => "N",
		"HIDE_ITEMS_WITHOUT_IMG" => "N",
		"DETAIL_STRICT_SECTION_CHECK" => "N",
		"SECTIONS_START_COLUMNS" => "10",
		"ELEMENT_SORT_FIELD_CUSTOM" => "CATALOG_AVAILABLE",
		"ELEMENT_SORT_ORDER_CUSTOM" => "asc",
		"VIP_ITEM_PROPERTY" => "-",
		"SECTION_BANNER_AREA_1" => "",
		"SECTION_BANNER_AREA_2" => "",
		"DETAIL_TITLE_TAB_VIDEO" => "Видеообзоры",
		"DETAIL_TITLE_TAB_STORES" => "Магазины",
		"DETAIL_TITLE_TAB_REVIEWS" => "Отзывы о товаре",
		"DETAIL_TITLE_TAB_DOCUMENTATION" => "Документация",
		"DETAIL_TITLE_TAB_CHARACTERISTICS" => "Характеристики",
		"DETAIL_TITLE_CHARISTICS_HEADER" => "Технические характеристики",
		"DETAIL_CNT_ELEMENTS_IN_SLIDERS" => "25",
		"ELEMENT_BANNER_AREA_0" => "",
		"ELEMENT_BANNER_AREA_1" => "",
		"ELEMENT_BANNER_AREA_2" => "",
		"ELEMENT_BANNER_AREA_3" => "",
		"ELEMENT_BANNER_AREA_4" => "",
		"ELEMENT_BANNER_AREA_5" => "",
		"COMPATIBLE_MODE" => "Y",
		"USE_ACTIONS_FUNCTIONAL" => "Y",
		"VALUE_RZ_AVAILABLE" => "В наличии",
		"BIGDATA_SECTION" => "Y",
		"RESIZER_REVIEWS_IMG" => "4",
		"IBLOCK_REVIEWS_TYPE" => "news",
		"PROP_FOR_REVIEWS_ITEM" => "-",
		"COUNT_REVIEWS_ITEM" => "5",
		"REVIEWS_TRUNCATE_LEN" => "100",
		"TITLE_TAB_REVIEWS_ITEM" => "Обзоры",
		"TYPE_3D" => "REVIEW3",
		"TYPE_SEARCH" => "Y",
		"TYPE_SEARCH_BY" => "SHT_CODE",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"FILTER_HIDE" => "N",
		"FILTER_SHOW_CNT" => "25",
		"CATALOG_ELEMENT_HITS_COUNT" => "4",
		"DETAIL_SHOW_VIDEO_IN_SLIDER" => "Y",
		"DETAIL_PROP_FOR_VIDEO_IN_SLIDER" => "-",
		"COMPLECTS_HEADER" => "Товары комплекта",
		"SEF_URL_TEMPLATES" => array(
			"sections" => "",
			"section" => "#SECTION_CODE#/",
			"element" => "#ELEMENT_CODE#.html",
			"compare" => "compare.php?action=#ACTION_CODE#",
			"smart_filter" => "#SECTION_CODE#/filter/#SMART_FILTER_PATH#/apply/",
		),
		"VARIABLE_ALIASES" => array(
			"compare" => array(
				"ACTION_CODE" => "action",
			),
		)
	),
	false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>