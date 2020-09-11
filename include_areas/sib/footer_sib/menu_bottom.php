<?global $rz_b2_options;
        if($rz_b2_options['hide_all_hrefs'] == 'Y' && ($APPLICATION->GetCurDir() == SITE_DIR.'personal/order/' || $APPLICATION->GetCurDir() == SITE_DIR.'personal/order/make/')) return;
        $APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"static", 
	array(
		"ROOT_MENU_TYPE" => "top",
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
        "CHILD_MENU_TYPE" => "top_sub",
		"MAX_LEVEL" => "2",
		"MENU_CACHE_TYPE" => "Y",
		"CACHE_TYPE" => "Y",
		"MENU_CACHE_TIME" => "31536000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"DELAY" => "N",
		"CACHE_SELECTED_ITEMS" => false,
		"ALLOW_MULTI_SELECT" => "N"
	),
	false
);