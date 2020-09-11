<?global $rz_b2_options?>
        <?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner", 
	"bitronic2", 
	array(
		"NUM" => $GLOBALS["rz_banner_num"]++,
		"TYPE" => "b2_catalog_element_double",
		"NOINDEX" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
            "ORDER_BANNER" => $rz_b2_options['order-sPrBannerTwo'],
		"COMPONENT_TEMPLATE" => "bitronic2",
		"QUANTITY" => "2"
	),
	false
);?>
