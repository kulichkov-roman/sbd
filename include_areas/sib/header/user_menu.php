<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"sib_user_header", 
	array(
		"ROOT_MENU_TYPE" => "user_sib",
		"MAX_LEVEL" => "2",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"MENU_CACHE_TYPE" => "Y",
		"CACHE_TYPE" => "Y",
		"MENU_CACHE_TIME" => "31536000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"DELAY" => "N",
		"CACHE_SELECTED_ITEMS" => false,
		"ALLOW_MULTI_SELECT" => "N",
		"COMPONENT_TEMPLATE" => "user_header",
		"RESIZER_SET" => "3",
		"PRICE_CODE" => ""
	),
	false
);