<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.compare.list", 
	"header", 
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
