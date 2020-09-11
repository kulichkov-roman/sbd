<? global $rz_b2_options; ?>
<? if($rz_b2_options['hide_all_hrefs'] == 'Y' && ($APPLICATION->GetCurDir() == SITE_DIR.'personal/order/' || $APPLICATION->GetCurDir() == SITE_DIR.'personal/order/make/')) return;
        $APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"catalog", 
	array(
		"ROOT_MENU_TYPE" => "catalog",
		"MAX_LEVEL" => "2",
		"CHILD_MENU_TYPE" => "top_sub",
		"CACHE_SELECTED_ITEMS" => false,
		"USE_EXT" => "Y",
		"CACHE_TYPE" => "Y",
		"MENU_CACHE_TYPE" => "Y",
		"MENU_CACHE_TIME" => "6048000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"VIEW_HIT" => $rz_b2_options["block_main-menu-elem"],
		"HITS_POSITION" => $rz_b2_options["menu-hits-position"],
		"SHOW_ICONS" => $rz_b2_options["menu-show-icons"],
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