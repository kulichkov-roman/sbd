<?global $rz_b2_options?>
        <?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner", 
	"bitronic2", 
	array(
		"TYPE" => "b2_index_double",
		"NOINDEX" => "Y",
		"PLACE_CLASS" => "container",
		"PLACE_CLASS" => "sBannerTwo",
		"CACHE_TIME" => "3600",
		"COMPONENT_TEMPLATE" => "bitronic2",
		"QUANTITY" => "2",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
