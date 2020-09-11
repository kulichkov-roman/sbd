<?
global $rz_b2_options;
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	"sib_bitronic2", 
	array(
		"NUM_CATEGORIES" => "2",
		"TOP_COUNT" => "5",
		"CHECK_DATES" => "Y",
		"SHOW_OTHERS" => "N",
		"PAGE" => "/catalog/",
		"CATEGORY_0_TITLE" => "Товары",
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"CATEGORY_0_iblock_catalog" => array(
			0 => "6",
		),
		"CATEGORY_1_TITLE" => "Новости",
		"CATEGORY_1" => array(
		),
		"CATEGORY_1_iblock_catalog" => array(
			0 => "all",
		),
		"CATEGORY_OTHERS_TITLE" => "Другое",
		"SHOW_INPUT" => "N",
		"CONTAINER_ID" => "search",
		"INPUT_ID" => "search-field",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"SHOW_PREVIEW" => "Y",
		"PREVIEW_WIDTH" => "75",
		"PREVIEW_HEIGHT" => "75",
		"CONVERT_CURRENCY" => "N",
		"ORDER" => "rank",
		"USE_LANGUAGE_GUESS" => "N",
		"RESIZER_SEARCH_TITLE" => "9",
		"PRICE_VAT_INCLUDE" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"CURRENCY_ID" => "RUB",
		"COMPONENT_TEMPLATE" => "bitronic2",
		"EXAMPLE_ENABLE" => "Y",
		"EXAMPLES" => array(
			0 => "Xiaomi"
		),
		"SHOW_CATEGORY_SWITCH" => ($rz_b2_options["block_search_category"]!=="N"?"Y":"N"),
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CATEGORY_1_iblock_news" => array(
			0 => "1",
		)
	),
	false
);