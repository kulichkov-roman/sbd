<?
global $rz_b2_options;
       $APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list", 
	"main", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "6",
		"COUNT_ELEMENTS" => "Y",
		"TOP_DEPTH" => "3",
		"SECTION_URL" => "",
		
        
        "CACHE_TYPE" => "A",
         "CATEGORIES_ORDER" => $rz_b2_options['order-sCategories'],
        "RESIZER_SECTION_BIG" => "29",
        "RESIZER_SECTION_LARGE" => "28",
		"CACHE_TIME" => "604800",
		"CACHE_GROUPS" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"VIEW_MODE" => "TEXT",
		"SHOW_PARENT_NAME" => "N",
		"COMPONENT_TEMPLATE" => "main",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"RESIZER_SECTION_ICON" => "9"
	),
	false
);
