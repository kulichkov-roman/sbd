<?global $rz_b2_options;
        if($rz_b2_options['hide_all_hrefs'] == 'Y' && ($APPLICATION->GetCurDir() == SITE_DIR.'personal/order/' || $APPLICATION->GetCurDir() == SITE_DIR.'personal/order/make/')) return;
        $APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"static", 
	array(
		"ROOT_MENU_TYPE" => "top",
		"MAX_LEVEL" => "3",
		"CHILD_MENU_TYPE" => "top_sub",
		"USE_EXT" => "N",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "604800",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"DELAY" => "N",
		"CACHE_SELECTED_ITEMS" => false,
		"ALLOW_MULTI_SELECT" => "N",
		"COMPONENT_TEMPLATE" => "static",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);