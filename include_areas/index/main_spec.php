<? global $rz_b2_options;

/*RBS_CUSTOM_START*/
if(\Bitrix\Main\Loader::includeModule('sib.core'))
{
	\Sib\Core\Regions::updateRegionStores();
}
/*RBS_CUSTOM_END*/
?>
<?$APPLICATION->IncludeComponent(
	"yenisite:main_spec", 
	"bitronic2", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "6",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"HIDE_NOTAVAILABLE" => "Y",
		"HIDE_WITHOUTPICTURE" => "Y",
		"STICKER_NEW" => "30",
		"STICKER_HIT" => "0",
		"STICKER_BESTSELLER" => "0",
		"DEFAULT_TAB" => "HIT",
		"TABS_INDEX" => "list",
		"RESIZER_SET_BIG" => "3",
		"ELEMENT_SORT_FIELD" => "rand",
		"ELEMENT_SORT_ORDER" => "asc",
		"LIST_PRICE_SORT" => "CATALOG_PRICE_1",
		"SHOW_ELEMENT" => "N",
		"OFFERS_FIELD_CODE" => ",",
		"OFFERS_PROPERTY_CODE" => ",",
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"PAGE_ELEMENT_COUNT" => "36",
		"LINE_ELEMENT_COUNT" => "4",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"BASKET_URL" => "/personal/cart/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"PRICE_CODE" => $_SESSION["VREGIONS_REGION"]["PRICE_CODE"],
		"STORES" => $_SESSION["VREGIONS_REGION"]["ID_SKLADA"],
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"USE_PRODUCT_QUANTITY" => "Y",
		"HIDE_BUY_IF_PROPS" => "N",
		"CONVERT_CURRENCY" => "Y",
		"OFFERS_CART_PROPERTIES" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"CACHE_GROUPS" => "Y",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"ADD_SECTIONS_CHAIN" => "N",
		"DISPLAY_COMPARE" => "Y",
		"COMPARE_PATH" => SITE_DIR."ajax/sib/compare.php",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "N",
		"PAGER_TEMPLATE" => "indicators",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"IBLOCK_MAX_VOTE" => "5",
		"IBLOCK_VOTE_NAMES" => array(
			0 => "1",
			1 => "2",
			2 => "3",
			3 => "4",
			4 => "5",
			5 => "",
		),
		"DISPLAY_AS_RATING" => "rating",
		"OFFER_TREE_PROPS" => array(
		),
		"PRODUCT_PROPERTIES" => "",
		"INCLUDE_JQUERY" => "Y",
		"SHOW_AMOUNT_STORE" => "N",
		"COMPONENT_TEMPLATE" => "bitronic2",
		"ARTICUL_PROP" => "CML2_ARTICLE",
		"PARTIAL_PRODUCT_PROPERTIES" => "Y",
		"STORE_DISPLAY_TYPE" => $rz_b2_options["store_amount_type"],
		"SB_FULL_DEFAULT" => $rz_b2_options["sb_full_default"],
		"SHOW_TABS" => array(
			0 => "HIT",
			1 => "SALE",
		),
		"IMAGE_SET" => "36",
		"TAB_PROPERTY_NEW" => "NEW",
		"TAB_PROPERTY_HIT" => "BESTSELLER",
		"TAB_PROPERTY_SALE" => "SALE",
		"TAB_PROPERTY_BESTSELLER" => "HIT",
		"TAB_SORT_NEW" => "100",
		"TAB_SORT_HIT" => "1",
		"TAB_SORT_SALE" => "10",
		"TAB_SORT_BESTSELLER" => "50",
		"TAB_TEXT_NEW" => "Новинки",
		"TAB_TEXT_HIT" => "Рекомендуем",
		"TAB_TEXT_SALE" => "Распродажа",
		"TAB_TEXT_BESTSELLER" => "Хит продаж",
		"DISPLAY_FAVORITE" => "N",
		"DISPLAY_ONECLICK" => "N",
		"HIDE_ICON_SLIDER" => "N",
		"RESIZER_SECTION_ICON" => "5",
		"CURRENCY_ID" => "RUB",
		"BLOCK_VIEW_MODE" => "",
		"COLOR_SCHEME" => "",
		"IMAGE_SET_BIG" => "",
		"PRODUCT_DISPLAY_MODE" => "",
		"USE_MOUSEWHEEL" => "",
		"MAIN_SP_ON_AUTO_NEW" => "N",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);