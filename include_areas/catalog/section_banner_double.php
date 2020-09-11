<?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner", 
	"bitronic2", 
	array(
		"NUM" => $GLOBALS["rz_banner_num"]++,
		"TYPE" => "b2_catalog_section_double",
		"NOINDEX" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"COMPONENT_TEMPLATE" => "bitronic2",
		"QUANTITY" => "2"
	),
	false
);?>
